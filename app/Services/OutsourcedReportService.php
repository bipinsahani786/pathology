<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OutsourcedReportService
{
    /**
     * Convert an uploaded PDF to cropped images (one per page).
     *
     * @param  string  $pdfPath       Storage path of the uploaded PDF
     * @param  int     $cropTopPct    Percentage to crop from top (remove external header)
     * @param  int     $cropBottomPct Percentage to crop from bottom (remove external footer)
     * @return array   Array of base64-encoded PNG image data URIs
     */
    public function convertAndCrop(string $pdfPath, int $cropTopPct = 18, int $cropBottomPct = 5): array
    {
        $images = [];

        if (!Storage::exists($pdfPath)) {
            Log::error("OutsourcedReportService: PDF not found in storage at {$pdfPath}");
            return [];
        }

        // Create a temporary file since Imagick and pdftoppm require local files (won't work with S3/R2 directly)
        $tempPdfPath = tempnam(sys_get_temp_dir(), 'outsourced_') . '.pdf';
        file_put_contents($tempPdfPath, Storage::get($pdfPath));

        try {
            // Use Imagick to convert PDF pages to images
            $imagick = new \Imagick();
            $imagick->setResolution(200, 200); // Good quality for report embedding
            $imagick->readImage($tempPdfPath);

            $pageCount = $imagick->getNumberImages();

            for ($i = 0; $i < $pageCount; $i++) {
                $imagick->setIteratorIndex($i);
                $imagick->setImageFormat('png');

                $width = $imagick->getImageWidth();
                $height = $imagick->getImageHeight();

                // Calculate crop dimensions
                $cropTop = (int) ($height * $cropTopPct / 100);
                $cropBottom = (int) ($height * $cropBottomPct / 100);
                $newHeight = $height - $cropTop - $cropBottom;

                if ($newHeight <= 0) {
                    $newHeight = $height; // Safety: don't crop if values are too large
                    $cropTop = 0;
                }

                // Crop: remove top header and bottom footer, keep middle (results + signature)
                $imagick->cropImage($width, $newHeight, 0, $cropTop);
                $imagick->setImagePage($width, $newHeight, 0, 0);

                $imageData = $imagick->getImageBlob();
                $base64 = base64_encode($imageData);
                $images[] = 'data:image/png;base64,' . $base64;
            }

            $imagick->clear();
            $imagick->destroy();

        } catch (\ImagickException $e) {
            Log::error("OutsourcedReportService Imagick error: " . $e->getMessage());

            // Fallback: Try using GD + shell command (pdftoppm)
            $images = $this->fallbackConvert($tempPdfPath, $cropTopPct, $cropBottomPct);
        } catch (\Throwable $e) {
            Log::error("OutsourcedReportService error: " . $e->getMessage());
        } finally {
            @unlink($tempPdfPath);
        }

        return $images;
    }

    /**
     * Fallback: Use pdftoppm (poppler-utils) if Imagick is not available.
     */
    private function fallbackConvert(string $fullPath, int $cropTopPct, int $cropBottomPct): array
    {
        $images = [];
        $tempDir = sys_get_temp_dir() . '/outsourced_' . uniqid();
        @mkdir($tempDir, 0755, true);

        try {
            // Convert PDF to PNG using pdftoppm
            $cmd = sprintf(
                'pdftoppm -png -r 200 %s %s/page',
                escapeshellarg($fullPath),
                escapeshellarg($tempDir)
            );
            exec($cmd, $output, $returnCode);

            if ($returnCode !== 0) {
                Log::error("pdftoppm failed with code {$returnCode}");
                return [];
            }

            // Process generated page images
            $files = glob($tempDir . '/page-*.png');
            sort($files);

            foreach ($files as $file) {
                $img = imagecreatefrompng($file);
                if (!$img) continue;

                $width = imagesx($img);
                $height = imagesy($img);

                $cropTop = (int) ($height * $cropTopPct / 100);
                $cropBottom = (int) ($height * $cropBottomPct / 100);
                $newHeight = max($height - $cropTop - $cropBottom, 1);

                $cropped = imagecrop($img, [
                    'x' => 0,
                    'y' => $cropTop,
                    'width' => $width,
                    'height' => $newHeight,
                ]);

                if ($cropped) {
                    ob_start();
                    imagepng($cropped);
                    $imageData = ob_get_clean();
                    $images[] = 'data:image/png;base64,' . base64_encode($imageData);
                    imagedestroy($cropped);
                }

                imagedestroy($img);
                @unlink($file);
            }
        } catch (\Throwable $e) {
            Log::error("Fallback PDF conversion error: " . $e->getMessage());
        }

        // Cleanup temp directory
        @rmdir($tempDir);

        return $images;
    }

    /**
     * Store an uploaded outsourced PDF file.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return string  Storage path
     */
    public function storePdf($file): string
    {
        return $file->store('outsourced-reports');
    }

    /**
     * Merge lab header and footer onto the existing PDF using FPDI.
     *
     * @param string $pdfPath
     * @param string|null $headerPath
     * @param string|null $footerPath
     * @return string Temporary path to the generated PDF
     */
    public function mergeHeaderFooter(string $pdfPath, ?string $headerPath, ?string $footerPath): string
    {
        if (!Storage::exists($pdfPath)) {
            Log::error("OutsourcedReportService: PDF not found in storage at {$pdfPath}");
            throw new \Exception("Uploaded PDF not found");
        }

        $pdf = new \setasign\Fpdi\Fpdi();
        
        $tempPdfPath = tempnam(sys_get_temp_dir(), 'outsourced_in_') . '.pdf';
        file_put_contents($tempPdfPath, Storage::get($pdfPath));

        $pageCount = $pdf->setSourceFile($tempPdfPath);

        $resolvedHeader = null;
        if ($headerPath && Storage::exists($headerPath)) {
            $resolvedHeader = $this->prepareImageForFpdf(Storage::get($headerPath));
        }

        $resolvedFooter = null;
        if ($footerPath && Storage::exists($footerPath)) {
            $resolvedFooter = $this->prepareImageForFpdf(Storage::get($footerPath));
        }

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);
            $orientation = $size['width'] > $size['height'] ? 'L' : 'P';
            
            $pdf->AddPage($orientation, [$size['width'], $size['height']]);

            // 1. Draw Header (Behind PDF)
            if ($resolvedHeader) {
                $pdf->Image($resolvedHeader, 0, 0, $size['width'], 0);
            }

            // 2. Draw Footer (Behind PDF)
            if ($resolvedFooter) {
                $sizeInfo = getimagesize($resolvedFooter);
                if ($sizeInfo) {
                    $imgWidth = $sizeInfo[0];
                    $imgHeight = $sizeInfo[1];
                    $renderedHeight = ($size['width'] / $imgWidth) * $imgHeight;
                    $yPos = $size['height'] - $renderedHeight;
                    $pdf->Image($resolvedFooter, 0, $yPos, $size['width'], 0);
                }
            }

            // 3. Draw original PDF page ON TOP of header and footer
            $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height']);
        }

        if ($resolvedHeader) @unlink($resolvedHeader);
        if ($resolvedFooter) @unlink($resolvedFooter);

        $outPath = tempnam(sys_get_temp_dir(), 'outsourced_out_') . '.pdf';
        $pdf->Output('F', $outPath);
        @unlink($tempPdfPath);

        return $outPath;
    }

    private function prepareImageForFpdf($imgData): ?string
    {
        if (!$imgData) return null;
        
        $baseTemp = tempnam(sys_get_temp_dir(), 'img_');
        file_put_contents($baseTemp, $imgData);
        
        $info = @getimagesize($baseTemp);
        $mime = $info['mime'] ?? '';
        
        if (in_array($mime, ['image/jpeg', 'image/png', 'image/gif'])) {
            $extMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
            $ext = $extMap[$mime];
            $finalPath = $baseTemp . '.' . $ext;
            rename($baseTemp, $finalPath);
            return $finalPath;
        }
        
        // Unsupported mime type or WEBP, try convert to PNG using GD
        $image = @imagecreatefromstring($imgData);
        if ($image !== false) {
            $finalPath = $baseTemp . '.png';
            imagealphablending($image, false);
            imagesavealpha($image, true);
            imagepng($image, $finalPath);
            imagedestroy($image);
            @unlink($baseTemp);
            return $finalPath;
        }
        
        @unlink($baseTemp);
        return null;
    }

    /**
     * Merge multiple PDF files into one.
     *
     * @param array $pdfPaths Array of absolute paths to temporary PDF files
     * @return string Temporary path to the merged PDF
     */
    public function mergePdfs(array $pdfPaths): string
    {
        $pdf = new \setasign\Fpdi\Fpdi();

        foreach ($pdfPaths as $path) {
            if (file_exists($path)) {
                $pageCount = $pdf->setSourceFile($path);
                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);
                    $orientation = $size['width'] > $size['height'] ? 'L' : 'P';
                    $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height']);
                }
            }
        }

        $outPath = tempnam(sys_get_temp_dir(), 'merged_out_') . '.pdf';
        $pdf->Output('F', $outPath);
        return $outPath;
    }
}
