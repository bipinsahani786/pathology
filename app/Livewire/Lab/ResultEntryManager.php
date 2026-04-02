<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use App\Models\Invoice;
use App\Models\TestReport;
use App\Models\ReportResult;
use Illuminate\Support\Facades\Log;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ResultEntryManager extends Component
{
    public $invoice;
    public $testReport;
    public $comments;
    
    public $results = [];
    public $highlights = [];
    public $flags = []; 
    public $parametersList = [];
    public $selectedTests = []; // For selective printing from here

    public function mount($id)
    {
        $this->authorize('view reports');
        $this->invoice = Invoice::with(['patient.patientProfile', 'items.labTest', 'testReport.results'])->findOrFail($id);
        
        // Ensure only users belonging to this company can access
        if ($this->invoice->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $this->testReport = $this->invoice->testReport;
        $this->comments = $this->testReport ? $this->testReport->comments : '';
        
        $this->initializeResultsData();
    }

    private function initializeResultsData()
    {
        $patient = $this->invoice->patient;
        $profile = $patient->patientProfile;
        $gender = strtolower($profile->gender ?? 'male');
        
        // Calculate/retrieve age for precise matching
        $ageDays = 0; $ageMonths = 0; $ageYears = 0;
        
        if ($profile && $profile->dob) {
            $dob = $profile->dob;
            $ageDays = now()->diffInDays($dob);
            $ageMonths = now()->diffInMonths($dob);
            $ageYears = now()->diffInYears($dob);
        } else {
            // Fallback to manual age fields
            $ageYears = (int)($profile->age ?? 0);
            $type = $profile->age_type ?? 'Years';
            if ($type === 'Months') {
                $ageMonths = $ageYears; // age field holds months
                $ageDays = $ageMonths * 30;
                $ageYears = 0;
            } elseif ($type === 'Days') {
                $ageDays = $ageYears; // age field holds days
                $ageMonths = 0; $ageYears = 0;
            } else {
                $ageMonths = $ageYears * 12;
                $ageDays = $ageYears * 365;
            }
        }

        $existingResultsMap = [];
        if ($this->testReport && $this->testReport->results) {
            foreach ($this->testReport->results as $r) {
                // Try matching by invoice_item_id first (new way), then fallback to lab_test_id (old way)
                if ($r->invoice_item_id) {
                    $key = $r->invoice_item_id . '_' . md5($r->parameter_name);
                } else {
                    $key = $r->lab_test_id . '_' . md5($r->parameter_name);
                }
                $existingResultsMap[$key] = $r;
            }
        }

        foreach ($this->invoice->items as $item) {
            if ($item->labTest && $item->labTest->parameters) {
                foreach ($item->labTest->parameters as $param) {
                    $paramName = is_array($param) ? ($param['name'] ?? 'Unknown') : $param;
                    $key = $item->id . '_' . md5($paramName); // Use invoice_item_id for unique mapping
                    
                    // NEW: Smart Range Matching
                    $matchedRange = $this->findMatchingRange($param, $gender, $ageDays, $ageMonths, $ageYears);
                    $refText = $matchedRange['display_range'] ?? $matchedRange['normal_value'] ?? '';
                    
                    // Fallback for old data structure if needed
                    if (empty($refText)) {
                        if ($gender === 'female') $refText = $param['female_range'] ?? $param['general_range'] ?? '';
                        else $refText = $param['male_range'] ?? $param['general_range'] ?? '';
                    }

                    $this->results[$key] = isset($existingResultsMap[$key]) ? $existingResultsMap[$key]->result_value : '';
                    $this->highlights[$key] = isset($existingResultsMap[$key]) ? $existingResultsMap[$key]->is_highlighted : false;
                    $this->flags[$key] = (isset($existingResultsMap[$key]) && in_array($existingResultsMap[$key]->status, ['High', 'Low'])) 
                        ? substr($existingResultsMap[$key]->status, 0, 1) 
                        : '';
                    
                    $this->parametersList[$key] = [
                        'key' => $key,
                        'lab_test_id' => $item->labTest->id,
                        'invoice_item_id' => $item->id,
                        'name' => $paramName,
                        'short_code' => $param['short_code'] ?? '',
                        'unit' => is_array($param) ? ($param['unit'] ?? '') : '',
                        'input_type' => $param['input_type'] ?? 'numeric',
                        'options' => $param['options'] ?? [],
                        'formula' => $param['formula'] ?? '',
                        'method' => $param['method'] ?? '',
                        'ref_range' => $refText,
                        'matched_range_details' => $matchedRange, // Keep for evaluation
                        'department' => $item->labTest->department,
                        'test_name' => $item->labTest->name,
                    ];
                }
            }
        }
    }

    private function findMatchingRange($param, $patientGender, $days, $months, $years)
    {
        if (!isset($param['ranges']) || !is_array($param['ranges'])) return null;

        foreach ($param['ranges'] as $range) {
            // 1. Gender check
            $rGender = strtolower($range['gender'] ?? 'both');
            if ($rGender !== 'both' && $rGender !== $patientGender) continue;

            // 2. Age check
            $unit = $range['age_unit'] ?? 'Years';
            $val = ($unit === 'Days') ? $days : (($unit === 'Months') ? $months : $years);
            
            if ($val >= ($range['age_min'] ?? 0) && $val <= ($range['age_max'] ?? 120)) {
                return $range;
            }
        }

        return null;
    }

    public function updatedResults($value, $key)
    {
        $this->autoCalculateFormulas();
        $this->autoEvaluateRanges();
    }

    private function autoCalculateFormulas()
    {
        // Group parameters by invoice_item_id to isolate calculation scope
        $groupedParams = [];
        foreach ($this->parametersList as $k => $p) {
            $itemId = $p['invoice_item_id'];
            $groupedParams[$itemId][$k] = $p;
        }

        $expressionLanguage = new ExpressionLanguage();

        foreach ($groupedParams as $itemId => $params) {
            // 1. Build a local code-to-value map for this test
            $localCodeMap = [];
            foreach ($params as $k => $p) {
                if (!empty($p['short_code'])) {
                    $localCodeMap[strtoupper($p['short_code'])] = (float)($this->results[$k] ?: 0);
                }
            }

            // 2. Process all calculated parameters for this test
            foreach ($params as $k => $p) {
                if ($p['input_type'] === 'calculated' && !empty($p['formula'])) {
                    $formula = strtoupper($p['formula']);
                    
                    // Clean up formula for ExpressionLanguage by removing braces {CODE} -> CODE
                    $formula = preg_replace('/\{([A-Z0-9_]+)\}/', '$1', $formula);
                    
                    try {
                        // Ensure formula is not empty
                        if (!empty(trim($formula))) {
                            // The expression language handles Division by Zero internally (throws exception)
                            $result = $expressionLanguage->evaluate($formula, $localCodeMap);
                            
                            if ($result !== false && is_numeric($result)) {
                                // Prevent saving INF or NAN
                                if (!is_infinite($result) && !is_nan($result)) {
                                    $this->results[$k] = round($result, 2);
                                }
                            }
                        }
                    } catch (\Throwable $e) {
                         Log::warning("Formula error for {$p['name']}: " . $e->getMessage());
                    }
                }
            }
        }
    }

    private function autoEvaluateRanges()
    {
        foreach ($this->results as $key => $val) {
            if ($val === '') {
                $this->flags[$key] = '';
                continue;
            }
            
            if (!isset($this->parametersList[$key])) {
                continue;
            }

            $param = $this->parametersList[$key];
            $range = $param['matched_range_details'] ?? null;
            $inputType = $param['input_type'] ?? 'numeric';

            $isAbnormal = false;
            $flag = '';

            if ($inputType === 'numeric' || $inputType === 'calculated') {
                $numVal = (float)$val;
                if ($range && !empty($range['min_val']) && !empty($range['max_val'])) {
                    if ($numVal < (float)$range['min_val']) {
                        $isAbnormal = true; $flag = 'L';
                    } elseif ($numVal > (float)$range['max_val']) {
                        $isAbnormal = true; $flag = 'H';
                    }
                }
            } elseif ($inputType === 'text' || $inputType === 'selection') {
                // Qualitative check
                if ($range && !empty($range['normal_value'])) {
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
        $this->highlights[$key] = !($this->highlights[$key] ?? false);
    }

    public function saveReport($status = 'Draft')
    {
        $this->authorize('edit reports');
        if (!$this->testReport) {
            $this->testReport = TestReport::create([
                'company_id' => $this->invoice->company_id,
                'invoice_id' => $this->invoice->id,
                'patient_id' => $this->invoice->patient_id,
                'status' => $status,
                'comments' => $this->comments,
                'approved_by' => $status === 'Approved' ? auth()->id() : null,
                'approved_at' => $status === 'Approved' ? now() : null,
            ]);
        } else {
            $this->testReport->update([
                'status' => $status,
                'comments' => $this->comments,
                'approved_by' => $status === 'Approved' ? auth()->id() : $this->testReport->approved_by,
                'approved_at' => $status === 'Approved' ? now() : $this->testReport->approved_at,
            ]);
        }

        // Save Results
        foreach ($this->parametersList as $key => $details) {
            $val = $this->results[$key] ?? '';
            $highlight = $this->highlights[$key] ?? false;

            // Determine textual status based on flags
            $flag = $this->flags[$key] ?? '';
            $stat = 'Normal';
            if ($flag === 'H') $stat = 'High';
            if ($flag === 'L') $stat = 'Low';

            ReportResult::updateOrCreate(
                [
                    'test_report_id' => $this->testReport->id,
                    'invoice_item_id' => $details['invoice_item_id'],
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
                ]
            );
        }

        if ($status === 'Approved') {
            $this->invoice->update(['sample_status' => 'Ready']);
            session()->flash('success', 'Report Approved Successfully and ready for printing.');
            return redirect()->route('lab.reports');
        } else {
            session()->flash('success', 'Draft Saved Successfully.');
        }
    }

    public function toggleTestStatus($itemId)
    {
        $this->authorize('edit reports');
        $item = \App\Models\InvoiceItem::findOrFail($itemId);
        $newStatus = $item->status === 'Completed' ? 'Pending' : 'Completed';
        $item->update(['status' => $newStatus]);
        
        // Refresh invoice to get updated status
        $this->invoice->load('items');
        
        session()->flash('success', "Test status updated to {$newStatus}.");
    }

    public function printSelected()
    {
        if (empty($this->selectedTests)) {
            session()->flash('error', 'Please select at least one test to print.');
            return;
        }

        $testIds = implode(',', $this->selectedTests);
        $url = route('lab.reports.print', ['id' => $this->invoice->id, 'template' => 'modern']) . '?tests=' . $testIds;
        
        $this->dispatch('open-new-tab', ['url' => $url]);
    }

    public function render()
    {
        // Group parameters by Department and then by Invoice Item ID to keep separate tests distinct
        $groupedParams = collect($this->parametersList)->groupBy('department')->map(function ($items) {
            return collect($items)->groupBy('invoice_item_id');
        });

        return view('livewire.lab.result-entry-manager', [
            'groupedParams' => $groupedParams
        ])->layout('layouts.app');
    }
}
