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
                    $paramName = is_array($param) ? ($param['param'] ?? $param['name'] ?? 'Unknown') : $param;
                    $key = $item->labTest->id . '_' . md5($paramName);
                    
                    // Determine reference range text
                    $refRange = '';
                    if ($gender === 'male' && !empty($param['male_range'])) {
                        $refRange = $param['male_range'];
                    } elseif ($gender === 'female' && !empty($param['female_range'])) {
                        $refRange = $param['female_range'];
                    } else {
                        // Fallback order: general -> male -> female -> custom range keys
                        $refRange = $param['general_range'] ?? $param['male_range'] ?? $param['female_range'] ?? $param['reference_range'] ?? $param['range'] ?? '';
                    }

                    // Existing or blank
                    $this->results[$key] = isset($existingResultsMap[$key]) ? $existingResultsMap[$key]->result_value : '';
                    $this->highlights[$key] = isset($existingResultsMap[$key]) ? $existingResultsMap[$key]->is_highlighted : false;
                    $this->flags[$key] = (isset($existingResultsMap[$key]) && in_array($existingResultsMap[$key]->status, ['High', 'Low'])) 
                        ? substr($existingResultsMap[$key]->status, 0, 1) 
                        : '';
                    
                    $this->parametersList[$key] = [
                        'lab_test_id' => $item->labTest->id,
                        'name' => $paramName,
                        'unit' => is_array($param) ? ($param['unit'] ?? '') : '',
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

        // --- CBC Formulas ---
        $hbKey = $keysByName['hemoglobin (hb)'] ?? $keysByName['hemoglobin'] ?? $keysByName['hb'] ?? null;
        $rbcKey = $keysByName['total rbc count'] ?? $keysByName['rbc count'] ?? $keysByName['rbc'] ?? $keysByName['total rbc'] ?? null;
        $pcvKey = $keysByName['packed cell volume (pcv)'] ?? $keysByName['pcv'] ?? $keysByName['packed cell volume'] ?? $keysByName['hct'] ?? null;
        $mcvKey = $keysByName['mean corpuscular volume (mcv)'] ?? $keysByName['mcv'] ?? null;
        $mchKey = $keysByName['mch'] ?? null;
        $mchcKey = $keysByName['mchc'] ?? null;

        if ($hbKey && $rbcKey && $pcvKey) {
            $hb = (float)($this->results[$hbKey] ?? 0);
            $rbc = (float)($this->results[$rbcKey] ?? 0);
            $pcv = (float)($this->results[$pcvKey] ?? 0);

            if ($rbc > 0) {
                // MCV = (PCV / RBC) * 10
                if ($mcvKey && ($this->results[$mcvKey] == '' || $this->results[$mcvKey] == '-')) {
                    $this->results[$mcvKey] = round(($pcv / $rbc) * 10, 2);
                }
                // MCH = (Hb / RBC) * 10
                if ($mchKey && ($this->results[$mchKey] == '' || $this->results[$mchKey] == '-')) {
                    $this->results[$mchKey] = round(($hb / $rbc) * 10, 2);
                }
            }
            if ($pcv > 0) {
                // MCHC = (Hb / PCV) * 100
                if ($mchcKey && ($this->results[$mchcKey] == '' || $this->results[$mchcKey] == '-')) {
                    $this->results[$mchcKey] = round(($hb / $pcv) * 100, 2);
                }
            }
        }
    }

    private function autoEvaluateRanges()
    {
        // Evaluate numerical bounds to auto-trigger High/Low flags
        foreach ($this->results as $key => $val) {
            if ($val === '' || !is_numeric($val)) {
                $this->flags[$key] = '';
                continue;
            }
            
            $ref = $this->parametersList[$key]['ref_range'] ?? '';
            $numVal = (float)$val;
            $isAbnormal = false;
            $flag = '';

            if (str_contains($ref, '-')) {
                $parts = explode('-', $ref);
                if (count($parts) == 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
                    if ($numVal < (float)$parts[0]) {
                        $isAbnormal = true;
                        $flag = 'L';
                    } elseif ($numVal > (float)$parts[1]) {
                        $isAbnormal = true;
                        $flag = 'H';
                    }
                }
            } elseif (str_contains($ref, '<')) {
                $limit = (float)str_replace('<', '', $ref);
                if ($numVal >= $limit) {
                    $isAbnormal = true;
                    $flag = 'H';
                }
            } elseif (str_contains($ref, '>')) {
                $limit = (float)str_replace('>', '', $ref);
                if ($numVal <= $limit) {
                    $isAbnormal = true;
                    $flag = 'L';
                }
            }

            $this->flags[$key] = $flag;
            // Auto toggle highlight if bounding fails
            if ($isAbnormal) {
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
