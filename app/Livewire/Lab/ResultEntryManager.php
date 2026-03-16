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
    
    // Arrays to hold our vital data bound to the frontend
    public $results = [];
    public $highlights = [];
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
        
        $existingResultsMap = [];
        if ($this->testReport && $this->testReport->results) {
            foreach ($this->testReport->results as $r) {
                // Key format: labTestId_parameterName (Replacing spaces with underscores for array keys)
                $key = $r->lab_test_id . '_' . md5($r->parameter_name);
                $existingResultsMap[$key] = $r;
            }
        }

        foreach ($this->invoice->items as $item) {
            if ($item->labTest && $item->labTest->parameters) {
                foreach ($item->labTest->parameters as $param) {
                    $key = $item->labTest->id . '_' . md5($param['param']);
                    
                    // Determine reference range text
                    $refRange = '';
                    if ($gender === 'male' && isset($param['male_range'])) {
                        $refRange = $param['male_range'];
                    } elseif ($gender === 'female' && isset($param['female_range'])) {
                        $refRange = $param['female_range'];
                    } else {
                        $refRange = $param['male_range'] ?? $param['female_range'] ?? '';
                    }

                    // Existing or blank
                    $this->results[$key] = isset($existingResultsMap[$key]) ? $existingResultsMap[$key]->result_value : '';
                    $this->highlights[$key] = isset($existingResultsMap[$key]) ? $existingResultsMap[$key]->is_highlighted : false;
                    
                    $this->parametersList[$key] = [
                        'lab_test_id' => $item->labTest->id,
                        'name' => $param['param'],
                        'unit' => $param['unit'] ?? '',
                        'ref_range' => $refRange,
                        'department' => $item->labTest->department,
                        'test_name' => $item->labTest->name,
                    ];
                }
            }
        }
    }

    public function updatedResults($value, $key)
    {
        $this->autoCalculateFormulas();
        $this->autoEvaluateRanges();
    }

    private function autoCalculateFormulas()
    {
        // Simple logic: if LDL is blank, calculate it. 
        // Real logic would parse formulas from the DB. 
        // Hardcoding standard formulas for demonstration (LalPathLabs style lipid profile).
        
        // Find keys by name to hook them up
        $keysByName = [];
        foreach ($this->parametersList as $k => $p) {
            $keysByName[strtolower($p['name'])] = $k;
        }

        $tcKey = $keysByName['total cholesterol'] ?? null;
        $hdlKey = $keysByName['hdl cholesterol'] ?? null;
        $tgKey = $keysByName['triglycerides'] ?? null;
        $ldlKey = $keysByName['ldl cholesterol'] ?? null;
        $vldlKey = $keysByName['vldl'] ?? null;

        if ($tcKey && $hdlKey && $tgKey && $vldlKey && $ldlKey) {
            $tc = (float)($this->results[$tcKey] ?? 0);
            $hdl = (float)($this->results[$hdlKey] ?? 0);
            $tg = (float)($this->results[$tgKey] ?? 0);

            if ($tc > 0 && $hdl > 0 && $tg > 0) {
                // VLDL = TG / 5
                $vldl = round($tg / 5, 2);
                $this->results[$vldlKey] = $vldl;

                // LDL = TC - (HDL + VLDL)
                $ldl = round($tc - ($hdl + $vldl), 2);
                $this->results[$ldlKey] = $ldl;
            }
        }
    }

    private function autoEvaluateRanges()
    {
        // Evaluate numerical bounds to auto-trigger High/Low flags
        foreach ($this->results as $key => $val) {
            if ($val === '' || !is_numeric($val)) continue;
            
            $ref = $this->parametersList[$key]['ref_range'] ?? '';
            $numVal = (float)$val;
            $isAbnormal = false;

            if (str_contains($ref, '-')) {
                $parts = explode('-', $ref);
                if (count($parts) == 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
                    if ($numVal < (float)$parts[0] || $numVal > (float)$parts[1]) {
                        $isAbnormal = true;
                    }
                }
            } elseif (str_contains($ref, '<')) {
                $limit = (float)str_replace('<', '', $ref);
                if ($numVal >= $limit) $isAbnormal = true;
            } elseif (str_contains($ref, '>')) {
                $limit = (float)str_replace('>', '', $ref);
                if ($numVal <= $limit) $isAbnormal = true;
            }

            // Auto toggle highlight if bounding fails
            if ($isAbnormal) {
                // Only auto-enable; don't auto-disable so user can manually override
                $this->highlights[$key] = true;
            } else {
                 $this->highlights[$key] = false;
            }
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

            // Determine textual status based on highlighting
            $stat = 'Normal';
            if ($highlight && is_numeric($val)) {
                $ref = $details['ref_range'];
                if (str_contains($ref, '-')) {
                    $parts = explode('-', $ref);
                    if (count($parts) == 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
                        if ((float)$val > (float)$parts[1]) $stat = 'High';
                        if ((float)$val < (float)$parts[0]) $stat = 'Low';
                    }
                } elseif (str_contains($ref, '<')) {
                    $limit = (float)str_replace('<', '', $ref);
                    if ((float)$val >= $limit) $stat = 'High';
                }
            }

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
