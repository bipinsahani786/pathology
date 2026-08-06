<?php

namespace App\Services\MachineIntegration;

use App\Models\Invoice;
use App\Models\MachineIntegration;
use App\Models\MachineResultLog;
use App\Models\ReportResult;
use App\Models\TestReport;
use Illuminate\Support\Facades\Log;

/**
 * ResultImporter
 *
 * Core service that:
 * 1. Receives parsed machine data
 * 2. Matches sample_id to invoice barcode
 * 3. Saves to machine_result_logs table
 * 4. Optionally auto-fills ReportResult rows
 */
class ResultImporter
{
    /**
     * Receive machine data, log it, and attempt to match to an invoice.
     *
     * @param  MachineIntegration $machine
     * @param  string             $rawData     Original raw string from machine
     * @param  array              $parsedData  Parsed key => {value, unit} array
     * @param  string|null        $sampleId    Extracted sample/barcode ID
     * @return MachineResultLog
     */
    public function receive(
        MachineIntegration $machine,
        string $rawData,
        array $parsedData,
        ?string $sampleId = null
    ): MachineResultLog {
        // Try to match invoice by barcode
        $invoice = null;
        if ($sampleId) {
            $invoice = Invoice::where('company_id', $machine->company_id)
                ->where('barcode', $sampleId)
                ->first();
        }

        $status = match (true) {
            $invoice !== null => 'matched',
            $sampleId !== null => 'unmatched',
            default            => 'pending',
        };

        $log = MachineResultLog::create([
            'machine_integration_id' => $machine->id,
            'company_id'             => $machine->company_id,
            'sample_id'              => $sampleId,
            'invoice_id'             => $invoice?->id,
            'raw_data'               => $rawData,
            'parsed_data'            => $parsedData,
            'status'                 => $status,
        ]);

        // Update machine heartbeat
        $machine->update(['last_seen_at' => now()]);

        Log::info("MachineResultImporter: {$machine->name} | Sample: {$sampleId} | Status: {$status}");

        return $log;
    }

    /**
     * Import a machine result log into the ReportResult table.
     * Called when user clicks "Import from Machine" on Result Entry page.
     *
     * @param  MachineResultLog $log
     * @param  int              $userId  Who clicked import
     * @return array{filled: int, skipped: int, unmatched_params: array}
     */
    public function importIntoReport(MachineResultLog $log, int $userId): array
    {
        if (!$log->invoice_id) {
            return ['filled' => 0, 'skipped' => 0, 'unmatched_params' => []];
        }

        $invoice = Invoice::with(['items.labTest', 'testReport.results'])
            ->find($log->invoice_id);

        if (!$invoice) {
            return ['filled' => 0, 'skipped' => 0, 'unmatched_params' => []];
        }

        // Ensure a TestReport exists
        $testReport = $invoice->testReport ?? TestReport::create([
            'company_id' => $invoice->company_id,
            'invoice_id' => $invoice->id,
            'patient_id' => $invoice->patient_id,
            'status'     => 'draft',
        ]);

        $machine      = $log->machine;
        $parsedData   = $log->parsed_data ?? [];
        $filled       = 0;
        $skipped      = 0;
        $unmatchedParams = [];

        // Build a map of existing ReportResult keys
        $existingMap = [];
        foreach ($testReport->results as $r) {
            $key = ($r->invoice_item_id ?? '0') . '_' . $r->lab_test_id . '_' . md5($r->parameter_name);
            $existingMap[$key] = $r;
        }

        foreach ($invoice->items as $item) {
            if (!$item->labTest) continue;

            $testsToProcess = collect();
            if ($item->labTest->is_package && !empty($item->labTest->linked_test_ids)) {
                $testsToProcess = \App\Models\LabTest::whereIn('id', $item->labTest->linked_test_ids)->get();
            } else {
                $testsToProcess = collect([$item->labTest]);
            }

            foreach ($testsToProcess as $test) {
                if (!$test->parameters) continue;

                foreach ($test->parameters as $param) {
                    $paramName = is_array($param) ? ($param['name'] ?? '') : $param;
                    if (!$paramName) continue;

                    // Map machine param name to our system param name
                    $machineParamKey = strtoupper($machine ? $machine->mapParam($paramName) : $paramName);

                    // Look up value in parsed data (case-insensitive)
                    $machineValue = $this->findInParsedData($parsedData, $machineParamKey, $paramName);

                    if ($machineValue === null) {
                        $unmatchedParams[] = $paramName;
                        continue;
                    }

                    $key = $item->id . '_' . $test->id . '_' . md5($paramName);

                    if (isset($existingMap[$key])) {
                        // Update existing result
                        $existingMap[$key]->update([
                            'result_value' => $machineValue,
                            'status'       => 'Normal', // Will be recalculated on save
                        ]);
                    } else {
                        // Create new result
                        ReportResult::create([
                            'test_report_id'  => $testReport->id,
                            'invoice_item_id' => $item->id,
                            'lab_test_id'     => $test->id,
                            'parameter_name'  => $paramName,
                            'result_value'    => $machineValue,
                            'unit'            => is_array($param) ? ($param['unit'] ?? '') : '',
                            'status'          => 'Normal',
                            'is_highlighted'  => false,
                        ]);
                    }

                    $filled++;
                }
            }
        }

        // Mark log as imported
        $log->markImported($userId);

        return [
            'filled'           => $filled,
            'skipped'          => $skipped,
            'unmatched_params' => array_unique($unmatchedParams),
        ];
    }

    /**
     * Find a value in parsed data by trying multiple key variations.
     */
    private function findInParsedData(array $parsedData, string ...$keys): ?string
    {
        foreach ($keys as $key) {
            // Try exact match
            if (isset($parsedData[$key])) {
                $val = $parsedData[$key];
                return is_array($val) ? ($val['value'] ?? null) : $val;
            }

            // Try uppercase
            $upper = strtoupper($key);
            if (isset($parsedData[$upper])) {
                $val = $parsedData[$upper];
                return is_array($val) ? ($val['value'] ?? null) : $val;
            }

            // Try lowercase
            $lower = strtolower($key);
            if (isset($parsedData[$lower])) {
                $val = $parsedData[$lower];
                return is_array($val) ? ($val['value'] ?? null) : $val;
            }
        }

        return null;
    }
}
