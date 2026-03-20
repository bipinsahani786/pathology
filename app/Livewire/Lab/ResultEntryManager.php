<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use App\Models\Invoice;
use App\Models\TestReport;
use App\Models\ReportResult;
use Illuminate\Support\Facades\Log;

class ResultEntryManager extends Component
{
    public $invoice;
    public $testReport;
    public $comments;
    
    public $results = [];
    public $highlights = [];
    public $flags = []; // New array to hold 'H' or 'L' flags
    public $parametersList = [];

    public function mount($id)
    {
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
                $key = $r->lab_test_id . '_' . md5($r->parameter_name);
                $existingResultsMap[$key] = $r;
            }
        }

        foreach ($this->invoice->items as $item) {
            if ($item->labTest && $item->labTest->parameters) {
                foreach ($item->labTest->parameters as $param) {
                    $paramName = is_array($param) ? ($param['name'] ?? 'Unknown') : $param;
                    $key = $item->labTest->id . '_' . md5($paramName);
                    
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
                        'lab_test_id' => $item->labTest->id,
                        'name' => $paramName,
                        'short_code' => $param['short_code'] ?? '',
                        'unit' => is_array($param) ? ($param['unit'] ?? '') : '',
                        'input_type' => $param['input_type'] ?? 'numeric',
                        'options' => $param['options'] ?? [],
                        'formula' => $param['formula'] ?? '',
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
        // 1. Build a code-to-value map
        $codeMap = [];
        foreach ($this->parametersList as $k => $p) {
            if (!empty($p['short_code'])) {
                $codeMap[strtoupper($p['short_code'])] = (float)($this->results[$k] ?: 0);
            }
        }

        // 2. Process all calculated parameters
        foreach ($this->parametersList as $k => $p) {
            if ($p['input_type'] === 'calculated' && !empty($p['formula'])) {
                $formula = strtoupper($p['formula']);
                
                // Replace codes like {HB} with values
                foreach ($codeMap as $code => $val) {
                    $formula = str_replace('{'.$code.'}', $val, $formula);
                }

                // Clean up any remaining braces or invalid chars
                $formula = preg_replace('/[^{}0-9\+\-\*\/\.\(\) ]/', '', $formula);
                
                try {
                    // Basic evaluation if formula looks safe
                    if (!empty($formula) && !str_contains($formula, '{')) {
                        // Prevent DivisionByZeroError by checking if '/ 0' exists in the string (approximate)
                        // Note: This is a simple protection for common cases.
                        if (preg_match('/\/ *0(\.0*)?($|[^0-9])/', $formula)) {
                             Log::warning("Division by zero in formula: $formula");
                             continue;
                        }

                        $result = @eval("return $formula;");
                        if ($result !== false && is_numeric($result)) {
                            $this->results[$k] = round($result, 2);
                        }
                    }
                } catch (\Throwable $e) {
                     Log::warning("Formula error for {$p['name']}: " . $e->getMessage());
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
                    'lab_test_id' => $details['lab_test_id'],
                    'parameter_name' => $details['name'],
                ],
                [
                    'result_value' => $val,
                    'status' => $stat,
                    'is_highlighted' => $highlight,
                    'reference_range' => $details['ref_range'],
                    'unit' => $details['unit'],
                ]
            );
        }

        if ($status === 'Approved') {
            session()->flash('success', 'Report Approved Successfully and ready for printing.');
            return redirect()->route('lab.reports');
        } else {
            session()->flash('success', 'Draft Saved Successfully.');
        }
    }

    public function render()
    {
        // Group parameters by Department and then by Test Name for rendering
        $groupedParams = collect($this->parametersList)->groupBy('department')->map(function ($items) {
            return collect($items)->groupBy('test_name');
        });

        return view('livewire.lab.result-entry-manager', [
            'groupedParams' => $groupedParams
        ])->layout('layouts.app');
    }
}
