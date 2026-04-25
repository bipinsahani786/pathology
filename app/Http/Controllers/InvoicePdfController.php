<?php

namespace App\Http\Controllers;

use App\Models\{Invoice, Company, Configuration};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRGdImagePNG;
use Picqer\Barcode\BarcodeGeneratorPNG;

class InvoicePdfController extends Controller
{
    public function download($id)
    {
        return $this->generateInvoice($id, true, true);
    }

    public function downloadWithoutHeader($id)
    {
        return $this->generateInvoice($id, false, false);
    }

    /**
     * Publicly stream an invoice PDF via QR code scan.
     */
    public function streamPublic($hash)
    {
        $id = base64_decode($hash);
        if (!$id || !is_numeric($id)) {
            abort(404, 'Invalid Bill Link');
        }

        $invoice = Invoice::find($id);
        if ($invoice && $invoice->pdf_path && \Illuminate\Support\Facades\Storage::disk('r2')->exists($invoice->pdf_path)) {
            $url = \Illuminate\Support\Facades\Storage::disk('r2')->url($invoice->pdf_path);
            return redirect($url);
        }

        return $this->generateInvoice($id, true, true, true);
    }

    private function generateInvoice($id, $showHeader = true, $showFooter = true, $isPublic = false)
    {
        // ── R2 Offload Check ────────────────────────────────────────────────
        // If this is a standard full invoice request, try to serve from R2
        /*
        if ($showHeader && $showFooter) {
            $invoice = Invoice::find($id);
            if ($invoice && $invoice->pdf_path && \Illuminate\Support\Facades\Storage::disk('r2')->exists($invoice->pdf_path)) {
                return redirect(\Illuminate\Support\Facades\Storage::disk('r2')->url($invoice->pdf_path));
            }
        }
        */

        $invoice = Invoice::with(['items', 'payments.paymentMode', 'patient.patientProfile', 'doctor.doctorProfile', 'collectionCenter', 'creator', 'company'])
            ->findOrFail($id);

        if (!$isPublic) {
            $user = auth()->user();
            
            // 1. Company Isolation
            if ($invoice->company_id !== $user->company_id) {
                abort(403, 'Unauthorized company access.');
            }

            // 2. Patient Isolation: Patients can only see their own invoices
            if ($user->hasRole('patient') && $invoice->patient_id !== $user->id) {
                abort(403, 'You are not authorized to view this invoice.');
            }

            // 3. Branch Isolation: If enabled, staff can only see their branch's invoices
            $companyId = $user->company_id;
            $restrictBranch = Configuration::getFor('restrict_branch_access', '1', $companyId) === '1';
            $isGlobalAdmin = $user->hasAnyRole(['lab_admin', 'super_admin']);

            if ($restrictBranch && !$isGlobalAdmin && $invoice->branch_id !== $user->branch_id) {
                abort(403, 'You do not have access to invoices from this branch.');
            }
        } else {
            $companyId = $invoice->company_id;
        }

        $company = $invoice->company;
        $template = Configuration::getFor('bill_template', 'classic', $companyId);
        
        $headerImage = Configuration::getFor('pdf_header_image', null, $companyId);
        $footerImage = Configuration::getFor('pdf_footer_image', null, $companyId);

        $view = 'pdf.invoice-' . $template;
        if (!view()->exists($view)) {
            $view = 'pdf.invoice-classic';
        }

        $pdfSettings = [
            'pdf_font_size'          => Configuration::getFor('pdf_font_size', null, $companyId) ?: 13,
            'pdf_font_family'        => Configuration::getFor('pdf_font_family', null, $companyId) ?: 'Helvetica',
            'pdf_margin_top'         => Configuration::getFor('pdf_margin_top', null, $companyId) ?: 310,
            'pdf_margin_bottom'      => Configuration::getFor('pdf_margin_bottom', null, $companyId) ?: 255,
            'pdf_header_height'      => Configuration::getFor('pdf_header_height', null, $companyId) ?: 200,
            'pdf_footer_height'      => Configuration::getFor('pdf_footer_height', null, $companyId) ?: 180,
            'pdf_header_image'       => ($showHeader && $headerImage) ? storage_base64($headerImage) : null,
            'pdf_footer_image'       => ($showFooter && $footerImage) ? storage_base64($footerImage) : null,
        ];

        // ── QR Code ──
        // Point to the public bill download route instead of the report route
        $publicUrl = route('public.bill.download', ['hash' => base64_encode($invoice->id)]);
        $options = new QROptions(['version' => 5, 'outputInterface' => QRGdImagePNG::class, 'eccLevel' => EccLevel::L, 'scale' => 4]);
        $qrCodeUri = (new QRCode($options))->render($publicUrl);

        // ── Barcode ──
        $generator = new BarcodeGeneratorPNG();
        $barcodeUri = 'data:image/png;base64,' . base64_encode($generator->getBarcode($invoice->invoice_number, $generator::TYPE_CODE_128, 1, 25));

        $pdf = Pdf::loadView($view, [
            'invoice'     => $invoice,
            'company'     => $company,
            'showHeader'  => $showHeader,
            'showFooter'  => $showFooter,
            'headerImage' => $showHeader ? $headerImage : null,
            'footerImage' => $showFooter ? $footerImage : null,
            'settings'    => $pdfSettings,
            'qrCodeUri'   => $qrCodeUri,
            'barcodeUri'  => $barcodeUri,
        ]);

        if ($template === 'thermal') {
            // 80mm width is approx 226pt. Height can be long (e.g. 800pt)
            $pdf->setPaper([0, 0, 226.77, 800], 'portrait');
        } else {
            $pdf->setPaper('a4', 'portrait');
        }

        return $pdf->stream('Invoice-' . $invoice->invoice_number . '.pdf');
    }

    public function previewTemplate($template)
    {
        $companyId = auth()->user()->company_id;
        $company = Company::find($companyId);
        
        $invoice = Invoice::where('company_id', $companyId)
            ->with(['items', 'payments.paymentMode', 'patient.patientProfile', 'doctor.doctorProfile', 'collectionCenter', 'creator'])
            ->latest()
            ->first();

        if (!$invoice) {
            // Generate dummy preview data for new labs
            $prefix = Configuration::getFor('invoice_prefix', 'PRE', $companyId);
            $invoice = new Invoice([
                'invoice_number' => $prefix . date('ym') . '-0001',
                'invoice_date' => now(),
                'payment_status' => 'Paid',
                'subtotal' => 1250.00,
                'discount_amount' => 125.00,
                'total_amount' => 1125.00,
                'paid_amount' => 1125.00,
                'due_amount' => 0.00,
                'membership_discount_amount' => 0,
                'voucher_discount_amount' => 0,
            ]);
            $invoice->id = 999;

            // Mock Patient (User with profile)
            $patient = new \App\Models\User(['name' => 'Sample Patient (John Doe)']);
            $profile = new \App\Models\PatientProfile([
                'age' => 30,
                'age_type' => 'Y',
                'gender' => 'Male',
                'patient_id_string' => 'PAT-1001'
            ]);
            $patient->setRelation('patientProfile', $profile);
            $invoice->setRelation('patient', $patient);

            // Mock Items
            $items = collect([
                new \App\Models\InvoiceItem(['test_name' => 'Complete Blood Count (CBC)', 'mrp' => 800.00, 'is_package' => false]),
                new \App\Models\InvoiceItem(['test_name' => 'Lipid Profile', 'mrp' => 450.00, 'is_package' => false]),
            ]);
            $invoice->setRelation('items', $items);
            
            // Mock empty relations
            $invoice->setRelation('doctor', null);
            $invoice->setRelation('collectionCenter', null);
            $invoice->setRelation('company', $company);
        }

        $view = 'pdf.invoice-' . $template;
        if (!view()->exists($view)) {
            $view = 'pdf.invoice-classic';
        }

        $pdfSettings = [
            'pdf_font_size'          => Configuration::getFor('pdf_font_size', null, $companyId) ?: 13,
            'pdf_font_family'        => Configuration::getFor('pdf_font_family', null, $companyId) ?: 'Helvetica',
            'pdf_margin_top'         => Configuration::getFor('pdf_margin_top', null, $companyId) ?: 310,
            'pdf_margin_bottom'      => Configuration::getFor('pdf_margin_bottom', null, $companyId) ?: 255,
            'pdf_header_height'      => Configuration::getFor('pdf_header_height', null, $companyId) ?: 200,
            'pdf_footer_height'      => Configuration::getFor('pdf_footer_height', null, $companyId) ?: 180,
            'pdf_header_image'       => null,
            'pdf_footer_image'       => null,
        ];

        // ── QR & Barcode ──
        $publicUrl = route('public.bill.download', ['hash' => base64_encode($invoice->id)]);
        $options = new QROptions(['version' => 5, 'outputInterface' => QRGdImagePNG::class, 'eccLevel' => EccLevel::L, 'scale' => 4]);
        $qrCodeUri = (new QRCode($options))->render($publicUrl);
        $generator = new BarcodeGeneratorPNG();
        $barcodeUri = 'data:image/png;base64,' . base64_encode($generator->getBarcode($invoice->invoice_number, $generator::TYPE_CODE_128, 1, 25));

        $pdf = Pdf::loadView($view, [
            'invoice'     => $invoice,
            'company'     => $company,
            'showHeader'  => true,
            'showFooter'  => true,
            'headerImage' => null,
            'footerImage' => null,
            'settings'    => $pdfSettings,
            'qrCodeUri'   => $qrCodeUri,
            'barcodeUri'  => $barcodeUri,
        ]);

        if ($template === 'thermal') {
            $pdf->setPaper([0, 0, 226.77, 800], 'portrait');
        } else {
            $pdf->setPaper('a4', 'portrait');
        }

        return $pdf->stream('Preview-' . ucfirst($template) . '-Template.pdf');
    }
}
