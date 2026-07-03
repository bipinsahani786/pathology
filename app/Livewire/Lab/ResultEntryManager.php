<?php

namespace App\Livewire\Lab;

use App\Models\Invoice;
use App\Models\ReportResult;
use App\Models\TestReport;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ResultEntryManager extends Component
{
    public $invoice;

    public $testReport;

    public $comments;

    public $results = [];

    public $cultureResults = [];

    public $highlights = [];

    public $flags = [];

    public $parametersList = [];

    public $selectedTests = []; // For selective printing from here

    public $testComments = []; // Comments per invoice item (test)

    public $report_date; // Custom report date for PDF

    public $report_time; // Custom report time for PDF

    public $manualOverrides = []; // Track calculated fields that were manually edited

    public function mount($id)
    {
        $this->authorize('view reports');
        $this->invoice = Invoice::where('company_id', auth()->user()->company_id)
            ->with(['patient.patientProfile', 'items.labTest', 'testReport.results'])
            ->findOrFail($id);

        $this->testReport = $this->invoice->testReport;
        $this->comments = $this->testReport ? $this->testReport->comments : '';

        // Initialize report date/time
        if ($this->testReport && $this->testReport->report_date) {
            $this->report_date = $this->testReport->report_date->format('Y-m-d');
            $this->report_time = $this->testReport->report_date->format('H:i');
        } elseif ($this->testReport && $this->testReport->approved_at) {
            $this->report_date = $this->testReport->approved_at->format('Y-m-d');
            $this->report_time = $this->testReport->approved_at->format('H:i');
        } else {
            $this->report_date = now()->format('Y-m-d');
            $this->report_time = now()->format('H:i');
        }

        $this->initializeResultsData();
    }

    private function initializeResultsData()
    {
        $patient = $this->invoice->patient;
        $profile = $patient->patientProfile;
        $gender = strtolower($profile->gender ?? 'male');

        // Calculate/retrieve age for precise matching
        $ageDays = 0;
        $ageMonths = 0;
        $ageYears = 0;

        if ($profile && $profile->dob) {
            $dob = $profile->dob;
            $ageDays = now()->diffInDays($dob);
            $ageMonths = now()->diffInMonths($dob);
            $ageYears = now()->diffInYears($dob);
        } else {
            // Fallback to manual age fields
            $ageYears = (int) ($profile->age ?? 0);
            $type = $profile->age_type ?? 'Years';
            if ($type === 'Months') {
                $ageMonths = $ageYears; // age field holds months
                $ageDays = $ageMonths * 30;
                $ageYears = 0;
            } elseif ($type === 'Days') {
                $ageDays = $ageYears; // age field holds days
                $ageMonths = 0;
                $ageYears = 0;
            } else {
                $ageMonths = $ageYears * 12;
                $ageDays = $ageYears * 365;
            }
        }

        $existingResultsMap = [];
        if ($this->testReport && $this->testReport->results) {
            foreach ($this->testReport->results as $r) {
                // Key format: invoice_item_id _ lab_test_id _ param_name_md5
                $key = ($r->invoice_item_id ?? '0').'_'.$r->lab_test_id.'_'.md5($r->parameter_name);
                $existingResultsMap[$key] = $r;
            }
        }

        foreach ($this->invoice->items as $item) {
            // Determine if report_comments is JSON (new granular format) or plain text (legacy)
            $rawComments = $item->report_comments ?? '';
            $decodedComments = json_decode($rawComments, true);
            $isJson = (json_last_error() === JSON_ERROR_NONE && is_array($decodedComments));

            // Handle Packages vs Single Tests
            $testsToProcess = [];
            if ($item->labTest) {
                if ($item->labTest->is_package && ! empty($item->labTest->linked_test_ids)) {
                    $testsToProcess = \App\Models\LabTest::whereIn('id', $item->labTest->linked_test_ids)->get();
                } else {
                    $testsToProcess = collect([$item->labTest]);
                }
            }

            foreach ($testsToProcess as $test) {
                // Load specific comment for this test in this invoice item
                $commentKey = $item->id.'_'.$test->id;
                if ($isJson) {
                    $this->testComments[$commentKey] = $decodedComments[$test->id] ?? '';
                } else {
                    // Legacy fallback: show the same comment for all tests in the item if it's not JSON
                    $this->testComments[$commentKey] = $rawComments;
                }

                if ($test->parameters) {
                    foreach ($test->parameters as $param) {
                        $paramName = is_array($param) ? ($param['name'] ?? 'Unknown') : $param;
                        $key = $item->id.'_'.$test->id.'_'.md5($paramName);

                        // NEW: Smart Range Matching
                        $matchedRange = $this->findMatchingRange($param, $gender, $ageDays, $ageMonths, $ageYears);
                        $refText = $matchedRange['display_range'] ?? $matchedRange['normal_value'] ?? '';

                        // Fallback for old data structure if needed
                        if (empty($refText)) {
                            if ($gender === 'female') {
                                $refText = $param['female_range'] ?? $param['general_range'] ?? '';
                            } else {
                                $refText = $param['male_range'] ?? $param['general_range'] ?? '';
                            }
                        }

                        $this->results[$key] = isset($existingResultsMap[$key]) ? $existingResultsMap[$key]->result_value : '';
                        $this->highlights[$key] = isset($existingResultsMap[$key]) ? $existingResultsMap[$key]->is_highlighted : false;
                        $this->flags[$key] = (isset($existingResultsMap[$key]) && in_array($existingResultsMap[$key]->status, ['High', 'Low']))
                            ? substr($existingResultsMap[$key]->status, 0, 1)
                            : '';

                        if (is_array($param) && ($param['input_type'] ?? 'numeric') === 'culture_sensitivity') {
                            $defaultAntibiotics = [
                                'Amikacin', 'Amoxicillin/Clavulanate', 'Ampicillin', 'Azithromycin', 
                                'Ceftriaxone', 'Cefotaxime', 'Ceftazidime', 'Cefuroxime', 'Ciprofloxacin', 
                                'Clindamycin', 'Cotrimoxazole', 'Erythromycin', 'Gentamicin', 'Imipenem', 
                                'Meropenem', 'Levofloxacin', 'Linezolid', 'Nitrofurantoin', 'Norfloxacin', 
                                'Ofloxacin', 'Penicillin', 'Piperacillin/Tazobactam', 'Tetracycline', 
                                'Tobramycin', 'Vancomycin'
                            ];

                            if (isset($existingResultsMap[$key]) && ! empty($existingResultsMap[$key]->culture_data)) {
                                $existingData = $existingResultsMap[$key]->culture_data;
                                $existingAbs = $existingData['antibiotics'] ?? [];
                                $existingNames = array_map('strtolower', array_column($existingAbs, 'name'));
                                
                                foreach($defaultAntibiotics as $abName) {
                                    if(!in_array(strtolower($abName), $existingNames)) {
                                        $existingAbs[] = [
                                            'name' => $abName,
                                            'sensitivity' => '',
                                            'mic' => ''
                                        ];
                                    }
                                }
                                $existingData['antibiotics'] = $existingAbs;
                                $this->cultureResults[$key] = $existingData;
                            } else {
                                $abList = [];
                                foreach($defaultAntibiotics as $abName) {
                                    $abList[] = [
                                        'name' => $abName,
                                        'sensitivity' => '',
                                        'mic' => ''
                                    ];
                                }

                                $this->cultureResults[$key] = [
                                    'organism_name' => '',
                                    'colony_count' => '',
                                    'growth_status' => 'Growth',
                                    'antibiotics' => $abList,
                                ];
                            }
                        }

                        $this->parametersList[$key] = [
                            'key' => $key,
                            'lab_test_id' => $test->id,
                            'invoice_item_id' => $item->id,
                            'name' => $paramName,
                            'short_code' => $param['short_code'] ?? '',
                            'unit' => is_array($param) ? ($param['unit'] ?? '') : '',
                            'input_type' => $param['input_type'] ?? 'numeric',
                            'options' => $param['options'] ?? [],
                            'formula' => $param['formula'] ?? '',
                            'method' => $param['method'] ?? '',
                            'ref_range' => $refText,
                            'matched_range_details' => $matchedRange,
                            'department' => $test->department,
                            'test_name' => $test->name,
                        ];
                    }
                }
            }
        }

        $this->detectManualOverrides();
    }

    private function findMatchingRange($param, $patientGender, $days, $months, $years)
    {
        if (! isset($param['ranges']) || ! is_array($param['ranges'])) {
            return null;
        }

        // 1. Try exact match (Gender + Age)
        foreach ($param['ranges'] as $range) {
            $rGender = strtolower($range['gender'] ?? 'both');
            if ($rGender !== 'both' && $rGender !== $patientGender) {
                continue;
            }

            $unit = $range['age_unit'] ?? 'Years';
            $val = ($unit === 'Days') ? $days : (($unit === 'Months') ? $months : $years);

            if ($val >= ($range['age_min'] ?? 0) && $val <= ($range['age_max'] ?? 150)) {
                return $range;
            }
        }

        // 2. Fallback 1: Try matching Gender only (widening age range)
        foreach ($param['ranges'] as $range) {
            $rGender = strtolower($range['gender'] ?? 'both');
            if ($rGender === $patientGender) {
                return $range;
            }
        }

        // 3. Fallback 2: Try matching 'Both' gender
        foreach ($param['ranges'] as $range) {
            if (strtolower($range['gender'] ?? '') === 'both') {
                return $range;
            }
        }

        // 4. Fallback 3: Return the first range available if any
        return count($param['ranges']) > 0 ? $param['ranges'][0] : null;
    }

    private function detectManualOverrides()
    {
        // Make a copy of results to test calculations
        $tempResults = $this->results;

        $groupedParams = [];
        foreach ($this->parametersList as $k => $p) {
            $itemId = $p['invoice_item_id'];
            $groupedParams[$itemId][$k] = $p;
        }

        $expressionLanguage = new ExpressionLanguage;

        foreach ($groupedParams as $itemId => $params) {
            $localCodeMap = [];
            foreach ($params as $k => $p) {
                if (! empty($p['short_code'])) {
                    $localCodeMap[strtoupper($p['short_code'])] = (float) ($tempResults[$k] ?: 0);
                }
            }

            foreach ($params as $k => $p) {
                if ($p['input_type'] === 'calculated' && ! empty($p['formula'])) {
                    $formula = strtoupper($p['formula']);
                    $formula = preg_replace('/\{([A-Z0-9_]+)\}/', '$1', $formula);

                    try {
                        if (! empty(trim($formula))) {
                            $result = $expressionLanguage->evaluate($formula, $localCodeMap);
                            if ($result !== false && is_numeric($result) && ! is_infinite($result) && ! is_nan($result)) {
                                $calcValue = round($result, 2);
                                $actualValue = isset($this->results[$k]) && $this->results[$k] !== '' ? (float) $this->results[$k] : null;

                                // If the DB value doesn't match what the formula says it should be, it was manually overridden
                                if ($actualValue !== null && $actualValue !== (float) $calcValue) {
                                    $this->manualOverrides[$k] = true;
                                }
                            }
                        }
                    } catch (\Throwable $e) {
                    }
                }
            }
        }
    }

    public function updatedResults($value, $key)
    {
        $param = $this->parametersList[$key] ?? null;
        if ($param && ($param['input_type'] ?? '') === 'calculated') {
            if ($value === '') {
                unset($this->manualOverrides[$key]);
            } else {
                $this->manualOverrides[$key] = true;
            }
        }

        $this->autoCalculateFormulas();
        $this->autoEvaluateRanges();

        if ($param) {
            $this->autoUpdateTestStatus($param['invoice_item_id']);
        }
    }

    public function updatedCultureResults($value, $key)
    {
        $parts = explode('.', $key);
        $paramKey = $parts[0] ?? null;
        if ($paramKey) {
            $param = $this->parametersList[$paramKey] ?? null;
            if ($param) {
                $this->autoUpdateTestStatus($param['invoice_item_id']);
            }
        }
    }

    private function autoCalculateFormulas()
    {
        // Group parameters by invoice_item_id to isolate calculation scope
        $groupedParams = [];
        foreach ($this->parametersList as $k => $p) {
            $itemId = $p['invoice_item_id'];
            $groupedParams[$itemId][$k] = $p;
        }

        $expressionLanguage = new ExpressionLanguage;

        foreach ($groupedParams as $itemId => $params) {
            // 1. Build a local code-to-value map for this test
            $localCodeMap = [];
            foreach ($params as $k => $p) {
                if (! empty($p['short_code'])) {
                    $localCodeMap[strtoupper($p['short_code'])] = (float) ($this->results[$k] ?: 0);
                }
            }

            // 2. Process all calculated parameters for this test
            foreach ($params as $k => $p) {
                if ($p['input_type'] === 'calculated' && ! empty($p['formula'])) {
                    if (! empty($this->manualOverrides[$k])) {
                        continue; // Skip calculating if manually overridden
                    }

                    $formula = strtoupper($p['formula']);

                    // Clean up formula for ExpressionLanguage by removing braces {CODE} -> CODE
                    $formula = preg_replace('/\{([A-Z0-9_]+)\}/', '$1', $formula);

                    try {
                        // Ensure formula is not empty
                        if (! empty(trim($formula))) {
                            // The expression language handles Division by Zero internally (throws exception)
                            $result = $expressionLanguage->evaluate($formula, $localCodeMap);

                            if ($result !== false && is_numeric($result)) {
                                // Prevent saving INF or NAN
                                if (! is_infinite($result) && ! is_nan($result)) {
                                    $this->results[$k] = round($result, 2);
                                }
                            }
                        }
                    } catch (\Throwable $e) {
                        Log::warning("Formula error for {$p['name']}: ".$e->getMessage());
                    }
                }
            }
        }
    }

    public function validateDlcSum()
    {
        $groupedParams = [];
        foreach ($this->parametersList as $k => $p) {
            $itemId = $p['invoice_item_id'] ?? null;
            if ($itemId) {
                $groupedParams[$itemId][$k] = $p;
            }
        }

        $dlcCodes = ['NEU', 'LYM', 'MONO', 'EOS', 'BASO'];

        foreach ($groupedParams as $itemId => $params) {
            $sum = 0;
            $hasDlc = false;
            $testName = '';

            foreach ($params as $k => $p) {
                $code = strtoupper($p['short_code'] ?? '');
                $testName = $p['test_name'] ?? 'CBC';
                if (in_array($code, $dlcCodes)) {
                    $hasDlc = true;
                    $sum += (float) ($this->results[$k] ?: 0);
                }
            }

            if ($hasDlc && abs($sum - 100) > 0.01) {
                return "Cannot approve report. The sum of DLC parameters (Neutrophils, Lymphocytes, Monocytes, Eosinophils, Basophils) in '{$testName}' is ".round($sum, 2).'%. It must be exactly 100%.';
            }
        }

        return true;
    }

    private function autoEvaluateRanges()
    {
        foreach ($this->results as $key => $val) {
            if ($val === '') {
                $this->flags[$key] = '';

                continue;
            }

            if (! isset($this->parametersList[$key])) {
                continue;
            }

            $param = $this->parametersList[$key];
            $range = $param['matched_range_details'] ?? null;
            $inputType = $param['input_type'] ?? 'numeric';

            $isAbnormal = false;
            $flag = '';

            if ($inputType === 'numeric' || $inputType === 'calculated') {
                $numVal = (float) $val;
                $min = $range['min_val'] ?? null;
                $max = $range['max_val'] ?? null;

                if ($range && (is_numeric($min) || is_numeric($max))) {
                    if (is_numeric($min) && $numVal < (float) $min) {
                        $isAbnormal = true;
                        $flag = 'L';
                    } elseif (is_numeric($max) && $numVal > (float) $max) {
                        $isAbnormal = true;
                        $flag = 'H';
                    }
                }
            } elseif ($inputType === 'text' || $inputType === 'selection') {
                // Qualitative check
                if ($range && ! empty($range['normal_value'])) {
                    if (strtolower(trim($val)) !== strtolower(trim($range['normal_value']))) {
                        $isAbnormal = true;
                        $flag = 'Abn';
                    }
                }
            }

            $this->flags[$key] = $flag;
            $this->highlights[$key] = $isAbnormal;
        }
    }

    public function toggleHighlight($key)
    {
        $this->highlights[$key] = ! ($this->highlights[$key] ?? false);
    }

    private function autoUpdateTestStatus($itemId)
    {
        $item = \App\Models\InvoiceItem::find($itemId);
        if (!$item) return;

        $testsData = [];
        $dlcCodes = ['NEU', 'LYM', 'MONO', 'EOS', 'BASO'];

        // Group results and DLC checks by lab_test_id
        foreach ($this->parametersList as $k => $p) {
            if (($p['invoice_item_id'] ?? null) == $itemId) {
                $testId = $p['lab_test_id'] ?? 0;
                if (!isset($testsData[$testId])) {
                    $testsData[$testId] = ['hasResult' => false, 'allFilled' => true, 'hasDlc' => false, 'dlcSum' => 0];
                }
                
                $inputType = $p['input_type'] ?? 'numeric';
                
                if ($inputType === 'heading') {
                    continue; // headings don't require values
                }

                $val = $this->results[$k] ?? '';
                $isFilled = ($val !== '' && $val !== null);
                
                // Check culture results
                if ($inputType === 'culture_sensitivity') {
                    $cData = $this->cultureResults[$k] ?? null;
                    if ($cData && (($cData['growth_status'] ?? '') === 'No Growth' || !empty($cData['organism_name']))) {
                        $isFilled = true;
                    } else {
                        $isFilled = false;
                    }
                }

                if ($isFilled) {
                    $testsData[$testId]['hasResult'] = true;
                } else {
                    $testsData[$testId]['allFilled'] = false;
                }
                
                $code = strtoupper($p['short_code'] ?? '');
                if (in_array($code, $dlcCodes)) {
                    $testsData[$testId]['hasDlc'] = true;
                    $testsData[$testId]['dlcSum'] += (float) ($val ?: 0);
                }
            }
        }

        $atLeastOneTestComplete = false;
        $hasAnyResultInAnyTest = false;
        $hasDlcError = false;
        
        foreach ($testsData as $data) {
            if ($data['hasResult']) {
                $hasAnyResultInAnyTest = true;
            }
            
            // If at least one test has all its parameters filled, the package can be marked completed
            if ($data['allFilled']) {
                $atLeastOneTestComplete = true;
            }
            
            if ($data['hasDlc'] && abs($data['dlcSum'] - 100) > 0.01) {
                $hasDlcError = true;
            }
        }

        if (empty($testsData)) {
            $atLeastOneTestComplete = false;
        }

        if ($item->status === 'Pending') {
            // Auto-complete if at least one test is fully filled (so partial printing is allowed)
            if ($atLeastOneTestComplete && !$hasDlcError) {
                $item->update(['status' => 'Completed']);
                if ($this->invoice) {
                    $this->invoice->load('items');
                }
            }
        } else {
            // It is currently Completed.
            // Downgrade to Pending if ALL tests are empty OR there's a DLC error
            if (!$hasAnyResultInAnyTest || $hasDlcError) {
                $item->update(['status' => 'Pending']);
                if ($this->invoice) {
                    $this->invoice->load('items');
                }
            }
        }
    }

    public function saveReport($status = 'Draft')
    {
        $this->authorize('edit reports');

        // Auto-evaluate and save test statuses before proceeding
        if ($this->invoice && $this->invoice->items) {
            foreach ($this->invoice->items as $item) {
                if (!empty($item->lab_test_id)) {
                    $this->autoUpdateTestStatus($item->id);
                }
            }
        }

        // Validation: Block approval if any results are missing or tests are not marked completed
        if ($status === 'Approved') {
            $items = $this->invoice->items;
            // Only check items that are actually tests (have lab_test_id)
            $tests = $items->filter(fn ($i) => ! empty($i->lab_test_id));
            $incompleteTests = $tests->filter(fn ($i) => $i->status !== 'Completed');

            if ($incompleteTests->count() > 0) {
                $msg = "Cannot approve report. All tests must be marked as 'Completed' first. (".$incompleteTests->pluck('test_name')->implode(', ').' are still pending)';
                $this->dispatch('notify', ['type' => 'error', 'message' => $msg]);
                session()->flash('error', $msg);

                return;
            }

            // Check if ANY results have been entered at all
            $hasAnyResult = false;
            foreach ($this->results as $key => $val) {
                if ($val !== '' && $val !== null) {
                    $hasAnyResult = true;
                    break;
                }
                
                // Also check if it's a culture sensitivity test with a valid result
                if (isset($this->parametersList[$key]) && ($this->parametersList[$key]['input_type'] ?? '') === 'culture_sensitivity') {
                    $cData = $this->cultureResults[$key] ?? null;
                    if ($cData) {
                        if (($cData['growth_status'] ?? '') === 'No Growth' || !empty($cData['organism_name'])) {
                            $hasAnyResult = true;
                            break;
                        }
                    }
                }
            }

            if (! $hasAnyResult && $tests->count() > 0) {
                $msg = 'Cannot approve report. No results have been entered for any test.';
                $this->dispatch('notify', ['type' => 'error', 'message' => $msg]);
                session()->flash('error', $msg);

                return;
            }

            // DLC Validation (Hard Block)
            $dlcValidation = $this->validateDlcSum();
            if ($dlcValidation !== true) {
                $this->dispatch('notify', ['type' => 'error', 'message' => $dlcValidation]);
                session()->flash('error', $dlcValidation);

                return;
            }
        }

        if (! $this->testReport) {
            $this->testReport = TestReport::create([
                'company_id' => $this->invoice->company_id,
                'invoice_id' => $this->invoice->id,
                'patient_id' => $this->invoice->patient_id,
                'status' => $status,
                'comments' => $this->comments,
                'approved_by' => $status === 'Approved' ? auth()->id() : null,
                'approved_at' => $status === 'Approved' ? now() : null,
                'report_date' => ($this->report_date && $this->report_time)
                    ? $this->report_date.' '.$this->report_time
                    : ($status === 'Approved' ? now() : null),
            ]);
        } else {
            $this->testReport->update([
                'status' => $status,
                'comments' => $this->comments,
                'approved_by' => $status === 'Approved' ? auth()->id() : $this->testReport->approved_by,
                'approved_at' => $status === 'Approved' ? now() : $this->testReport->approved_at,
                'report_date' => ($this->report_date && $this->report_time)
                    ? $this->report_date.' '.$this->report_time
                    : $this->testReport->report_date,
            ]);
        }

        // Save Results
        if ($this->testReport) {
            // Delete existing results for this report to prevent duplicates/ghost data from modified tests
            ReportResult::where('test_report_id', $this->testReport->id)->delete();
        }

        foreach ($this->parametersList as $key => $details) {
            $val = $this->results[$key] ?? '';
            $highlight = $this->highlights[$key] ?? false;

            // Determine textual status based on flags
            $flag = $this->flags[$key] ?? '';
            $stat = 'Normal';
            if ($flag === 'H') {
                $stat = 'High';
            }
            if ($flag === 'L') {
                $stat = 'Low';
            }

            $cultureData = null;
            if (($details['input_type'] ?? 'numeric') === 'culture_sensitivity') {
                $cultureData = $this->cultureResults[$key] ?? null;
                if ($cultureData) {
                    if (($cultureData['growth_status'] ?? '') === 'No Growth') {
                        $val = 'No Growth';
                        $cultureData['antibiotics'] = [];
                    } else {
                        $val = ($cultureData['organism_name'] ?? 'Organism') . ' (' . ($cultureData['colony_count'] ?? '0') . ')';
                        if(isset($cultureData['antibiotics'])) {
                            $cultureData['antibiotics'] = array_values(array_filter($cultureData['antibiotics'], function($ab) {
                                return !empty($ab['sensitivity']) || !empty($ab['mic']);
                            }));
                        }
                    }
                }
            }

            ReportResult::updateOrCreate(
                [
                    'test_report_id' => $this->testReport->id,
                    'invoice_item_id' => $details['invoice_item_id'],
                    'lab_test_id' => $details['lab_test_id'],
                    'parameter_name' => $details['name'],
                ],
                [
                    'lab_test_id' => $details['lab_test_id'],
                    'result_value' => $val,
                    'status' => $stat,
                    'is_highlighted' => $highlight,
                    'reference_range' => $details['ref_range'],
                    'unit' => $details['unit'],
                    'method' => $details['method'] ?? null,
                    'culture_data' => $cultureData,
                ]
            );
        }

        // Save Test Level Comments (Granular for packages)
        foreach ($this->invoice->items as $item) {
            $itemComments = [];
            $hasGranular = false;

            // Collect all comments belonging to this item
            foreach ($this->testComments as $key => $comment) {
                if (str_starts_with($key, $item->id.'_')) {
                    $testId = substr($key, strlen($item->id.'_'));
                    $itemComments[$testId] = $comment;
                    $hasGranular = true;
                }
            }

            if ($hasGranular) {
                // If it's a single test (not package) AND only one comment exists, store as plain text for backward compatibility
                if (! $item->labTest->is_package && count($itemComments) === 1) {
                    $item->update(['report_comments' => reset($itemComments)]);
                } else {
                    // Store as JSON for packages or multiple entries
                    $item->update(['report_comments' => json_encode($itemComments)]);
                }
            }
        }

        if ($status === 'Approved') {
            $this->invoice->update(['sample_status' => 'Ready']);
            $msg = 'Report Approved Successfully and ready for printing.';
            session()->flash('success', $msg);
            $this->dispatch('notify', ['type' => 'success', 'message' => $msg]);

            // Pre-generate PDF for R2 offloading
            try {
                $pdfService = new \App\Services\PdfStorageService;
                $pdfService->storeReportPdf($this->testReport);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to pre-generate PDF: '.$e->getMessage());
            }

            return redirect()->route('lab.reports');
        } else {
            $msg = 'Draft Saved Successfully.';
            session()->flash('success', $msg);
            $this->dispatch('notify', ['type' => 'success', 'message' => $msg]);
        }
    }

    public function toggleTestStatus($itemId)
    {
        $this->authorize('edit reports');
        $item = \App\Models\InvoiceItem::findOrFail($itemId);
        $newStatus = $item->status === 'Completed' ? 'Pending' : 'Completed';

        if ($newStatus === 'Completed') {
            $dlcCodes = ['NEU', 'LYM', 'MONO', 'EOS', 'BASO'];
            $sum = 0;
            $hasDlc = false;

            foreach ($this->parametersList as $k => $p) {
                if (($p['invoice_item_id'] ?? null) == $itemId) {
                    $code = strtoupper($p['short_code'] ?? '');
                    if (in_array($code, $dlcCodes)) {
                        $hasDlc = true;
                        $sum += (float) ($this->results[$k] ?: 0);
                    }
                }
            }

            if ($hasDlc && abs($sum - 100) > 0.01) {
                $msg = 'Cannot mark test as complete. The sum of DLC parameters must be exactly 100% (Current sum: '.round($sum, 2).'%).';
                $this->dispatch('notify', ['type' => 'error', 'message' => $msg]);
                session()->flash('error', $msg);

                return;
            }
        }

        $item->update(['status' => $newStatus]);

        // Refresh invoice to get updated status
        $this->invoice->load('items');

        $msg = "Test status updated to {$newStatus}.";
        session()->flash('success', $msg);
        $this->dispatch('notify', ['type' => 'success', 'message' => $msg]);
    }

    public function printSelected($withHeader = 1)
    {
        if (empty($this->selectedTests)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Please select at least one test to print.']);
            session()->flash('error', 'Please select at least one test to print.');

            return;
        }

        // Printing proceeds regardless of image presence to allow for physical letterhead space
        $testIds = is_array($this->selectedTests) ? implode(',', $this->selectedTests) : $this->selectedTests;
        $url = route('lab.reports.print', ['id' => $this->invoice->id, 'template' => 'new'])
            .'?tests='.$testIds
            .'&header='.($withHeader ? '1' : '0')
            .'&t='.time();

        $this->dispatch('open-new-tab', ['url' => $url]);
    }

    public function addAntibioticRow($key)
    {
        if (! isset($this->cultureResults[$key])) {
            $this->cultureResults[$key] = [
                'organism_name' => '',
                'colony_count' => '',
                'growth_status' => 'Growth',
                'antibiotics' => [],
            ];
        }
        $this->cultureResults[$key]['antibiotics'][] = [
            'name' => '',
            'sensitivity' => 'S',
            'mic' => '',
        ];
    }

    public function removeAntibioticRow($key, $index)
    {
        if (isset($this->cultureResults[$key]['antibiotics'][$index])) {
            unset($this->cultureResults[$key]['antibiotics'][$index]);
            $this->cultureResults[$key]['antibiotics'] = array_values($this->cultureResults[$key]['antibiotics']);
        }
    }

    public function clearAntibioticRow($key, $index)
    {
        if (isset($this->cultureResults[$key]['antibiotics'][$index])) {
            $this->cultureResults[$key]['antibiotics'][$index]['sensitivity'] = '';
            $this->cultureResults[$key]['antibiotics'][$index]['mic'] = '';
        }
    }

    public function render()
    {
        // Group parameters by Department -> Invoice Item (Bill Line) -> Lab Test (Actual Test Name)
        $groupedParams = collect($this->parametersList)->groupBy('department')->map(function ($items) {
            return collect($items)->groupBy('invoice_item_id')->map(function ($testGroup) {
                return collect($testGroup)->groupBy('lab_test_id');
            });
        });

        return view('livewire.lab.result-entry-manager', [
            'groupedParams' => $groupedParams,
        ])->layout('layouts.app');
    }
}
