<?php

namespace App\Services;

use Illuminate\Support\Collection;

class ReportLayoutOptimizer
{
    /**
     * Determine if smart layout optimization should be executed.
     */
    public static function shouldOptimize(array $settings): bool
    {
        $smartPacking = !empty($settings['report_smart_page_packing']) && $settings['report_smart_page_packing'] !== '0';
        $style = $settings['report_page_break_style'] ?? 'continuous';

        return $smartPacking || $style === 'continuous_optimized';
    }

    /**
     * Estimate rendered height in pixels for a single test block in DomPDF (96 DPI).
     */
    public static function estimateTestHeight(array $testData, array $settings = []): int
    {
        $results = $testData['results'] ?? collect();
        $labTest = $testData['labTest'] ?? null;
        $testName = $testData['name'] ?? '';

        $isWidalSlide = ($labTest && $labTest->test_code === 'WIDAL_SLIDE')
            || (stripos($testName, 'widal') !== false && stripos($testName, 'slide') !== false)
            || ($results->contains(fn($r) => is_array($r->culture_data) && ($r->culture_data['type'] ?? '') === 'widal_slide'));

        $isCulture = $results->isNotEmpty() && $results->contains(fn($r) => !empty($r->culture_data) && (($r->culture_data['type'] ?? '') !== 'widal_slide'));

        // Base header overhead: dept title (~30px) + test title (~28px) + table header (~28px) + margin bottom (~28px)
        $height = 114;

        if (!empty($labTest?->method)) {
            $height += 18;
        }

        if ($isWidalSlide) {
            $height += 200; // Widal slide 6-column dilution table + result box
        } elseif ($isCulture) {
            $cResult = $results->first(fn($r) => !empty($r->culture_data));
            $cData = is_array($cResult?->culture_data) ? $cResult->culture_data : [];
            $growth = $cData['growth_status'] ?? 'Growth';
            if ($growth === 'No Growth') {
                $height += 95;
            } else {
                $abCount = count($cData['antibiotics'] ?? []);
                $height += 95 + (int) (ceil($abCount / 2) * 22);
            }
        } else {
            // Standard tabular parameters
            foreach ($results as $r) {
                $isSubHeader = (is_null($r->result_value) || trim($r->result_value) === '')
                    && (is_null($r->reference_range) || trim($r->reference_range) === '');
                if ($isSubHeader) {
                    $height += 24;
                } else {
                    $height += 26;
                }
            }
        }

        // Clinical Interpretation
        $showInterp = ($settings['report_show_interpretation'] ?? true);
        $interp = !empty(trim(strip_tags($testData['remark'] ?? '', '<img>')))
            ? $testData['remark']
            : ($labTest?->interpretation ?? null);
        if ($showInterp && !empty(trim(strip_tags($interp ?? '', '<img>')))) {
            $cleanInterp = trim(strip_tags($interp));
            $lines = max(1, (int) ceil(strlen($cleanInterp) / 75));
            $height += 35 + ($lines * 15);
        }

        // Lab Test Note
        $showNote = ($settings['report_show_note'] ?? true);
        if ($showNote && !empty($labTest?->description)) {
            $cleanNote = trim(strip_tags($labTest->description));
            $lines = max(1, (int) ceil(strlen($cleanNote) / 75));
            $height += 30 + ($lines * 15);
        }

        return $height;
    }

    /**
     * Calculate printable page capacity in pixels.
     */
    public static function getPageCapacity(array $settings): int
    {
        $marginTop = (int) ($settings['pdf_margin_top'] ?? 310);
        $marginBottom = (int) ($settings['pdf_margin_bottom'] ?? 255);

        // A4 page height at 96 DPI is ~1122.5px
        // Deduct 50px buffer to ensure tests fit comfortably without tight clipping
        return max(320, 1122 - $marginTop - $marginBottom - 50);
    }

    /**
     * Optimize grouped results to minimize total pages by bin-packing tests into best-fit pages.
     */
    public static function optimizeGroupedResults(Collection $groupedResults, array $settings): Collection
    {
        if (!self::shouldOptimize($settings)) {
            return $groupedResults;
        }

        // 1. Flatten all tests from existing groups while capturing department metadata and original order
        $flattenedTests = [];
        $originalOrder = 0;

        foreach ($groupedResults as $groupKey => $groupData) {
            $dept = $groupData['department'] ?? null;
            $tests = $groupData['tests'] ?? collect();

            foreach ($tests as $testKey => $testData) {
                $flattenedTests[] = [
                    'test_key' => $testKey,
                    'test_data' => $testData,
                    'dept' => $dept,
                    'original_order' => $originalOrder++,
                    'height' => self::estimateTestHeight($testData, $settings),
                ];
            }
        }

        if (count($flattenedTests) <= 1) {
            return $groupedResults;
        }

        $capacity = self::getPageCapacity($settings);
        $unplaced = $flattenedTests;
        $pages = [];

        // 2. Bin-pack tests into pages
        while (!empty($unplaced)) {
            // Start a new page with the next available test in sequence
            $firstKey = array_key_first($unplaced);
            $firstItem = $unplaced[$firstKey];
            unset($unplaced[$firstKey]);

            $currentPageTests = [$firstItem];
            $currentHeight = $firstItem['height'];

            // If the first test alone fills or exceeds the page capacity, close this page
            if ($currentHeight < $capacity) {
                // Look ahead across all remaining unplaced tests to find best fits
                while (true) {
                    $remainingSpace = $capacity - $currentHeight;
                    $bestCandidateKey = null;
                    $bestCandidateScore = -1;

                    foreach ($unplaced as $k => $candidate) {
                        if ($candidate['height'] <= $remainingSpace) {
                            // Scoring:
                            // We prefer tests that utilize the space well (larger height),
                            // and give a significant bonus (+80) if from the same department as tests already on this page.
                            $sameDeptBonus = 0;
                            foreach ($currentPageTests as $pt) {
                                if (($pt['dept']?->id ?? null) === ($candidate['dept']?->id ?? null)) {
                                    $sameDeptBonus = 80;
                                    break;
                                }
                            }

                            $score = $candidate['height'] + $sameDeptBonus;

                            if ($score > $bestCandidateScore) {
                                $bestCandidateScore = $score;
                                $bestCandidateKey = $k;
                            }
                        }
                    }

                    if ($bestCandidateKey !== null) {
                        $chosen = $unplaced[$bestCandidateKey];
                        unset($unplaced[$bestCandidateKey]);
                        $currentPageTests[] = $chosen;
                        $currentHeight += $chosen['height'];
                    } else {
                        // No more unplaced tests fit into this page
                        break;
                    }
                }
            }

            $pages[] = $currentPageTests;
        }

        // 3. Rebuild $groupedResults structure keyed by page index
        // Each page becomes a distinct group in $groupedResults with its tests
        $optimizedGroupedResults = collect();

        foreach ($pages as $pageIndex => $pageItems) {
            $testsCollection = collect();

            foreach ($pageItems as $itemIndex => $item) {
                $testData = $item['test_data'];
                $testData['dept'] = $item['dept'];
                $testData['can_fit_single_page'] = ($item['height'] <= $capacity);
                // Set explicit page break before the first test of every page after page 1
                $testData['page_break_before'] = ($pageIndex > 0 && $itemIndex === 0);

                $testsCollection->put($item['test_key'], $testData);
            }

            $optimizedGroupedResults->put('page_' . $pageIndex, [
                'department' => $pageItems[0]['dept'] ?? null,
                'page_index' => $pageIndex,
                'tests' => $testsCollection,
            ]);
        }

        return $optimizedGroupedResults;
    }
}
