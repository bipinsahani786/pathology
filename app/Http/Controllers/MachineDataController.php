<?php

namespace App\Http\Controllers;

use App\Models\MachineIntegration;
use App\Services\MachineIntegration\MachineParserFactory;
use App\Services\MachineIntegration\ResultImporter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * MachineDataController
 *
 * Secure API endpoint that receives data from:
 * 1. Bridge Agent running on lab PC (for serial/TCP machines)
 * 2. Machines that support direct HTTP push (AURA Chem 120, Cellomax 5)
 *
 * Authentication: Bearer token (machine's api_token field)
 */
class MachineDataController extends Controller
{
    public function __construct(protected ResultImporter $importer) {}

    // ─────────────────────────────────────────────────────────────
    // POST /api/machine/push-result
    // ─────────────────────────────────────────────────────────────

    /**
     * Receive parsed result data from Bridge Agent or machine.
     *
     * Request body (JSON):
     * {
     *   "sample_id": "LAB260801001",
     *   "machine_type": "hematology",   // optional, override
     *   "results": {
     *     "WBC": "7.2",
     *     "RBC": "4.85",
     *     "HGB": "14.2"
     *   }
     * }
     */
    public function pushResult(Request $request): JsonResponse
    {
        $machine = $this->authenticateMachine($request);
        if (!$machine) {
            return response()->json(['error' => 'Unauthorized. Invalid or missing API token.'], 401);
        }

        $validated = $request->validate([
            'sample_id' => 'nullable|string|max:100',
            'results'   => 'required|array|min:1',
        ]);

        $sampleId   = $validated['sample_id'] ?? null;
        $rawResults = $validated['results'];

        // Store parsed data in normalized format
        $parsedData = [];
        foreach ($rawResults as $key => $value) {
            $parsedData[strtoupper($key)] = [
                'value' => (string) $value,
                'unit'  => '',
            ];
        }

        $log = $this->importer->receive(
            machine:     $machine,
            rawData:     json_encode($rawResults),
            parsedData:  $parsedData,
            sampleId:    $sampleId,
        );

        $response = [
            'status'  => $log->status,
            'log_id'  => $log->id,
            'message' => match ($log->status) {
                'matched'   => "Sample matched to invoice #{$log->invoice_id}. Ready to import.",
                'unmatched' => "Sample ID '{$sampleId}' not found. Results stored in pending queue.",
                default     => 'Results received and stored.',
            },
        ];

        if ($log->invoice) {
            $response['invoice_id']   = $log->invoice_id;
            $response['patient_name'] = $log->invoice->patient?->name ?? 'Unknown';
        }

        return response()->json($response, 200);
    }

    // ─────────────────────────────────────────────────────────────
    // POST /api/machine/push-raw
    // ─────────────────────────────────────────────────────────────

    /**
     * Receive raw ASTM / proprietary string from Bridge Agent.
     * The server does the parsing.
     *
     * Request body (JSON):
     * {
     *   "raw_data": "H|\\^&|||AURA^Chem120...\rR|1|^^^SGOT|45.2|U/L|10-40||||F\rL|1|N"
     * }
     */
    public function pushRaw(Request $request): JsonResponse
    {
        $machine = $this->authenticateMachine($request);
        if (!$machine) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        $validated = $request->validate([
            'raw_data' => 'required|string',
        ]);

        $rawData = $validated['raw_data'];

        try {
            $parsed   = MachineParserFactory::parse($machine, $rawData);
            $sampleId = $parsed['sample_id'] ?? null;

            // Convert to flat array for storage
            $parsedData = [];
            foreach ($parsed['results'] as $key => $data) {
                $parsedData[strtoupper($key)] = is_array($data)
                    ? ['value' => $data['value'] ?? '', 'unit' => $data['unit'] ?? '']
                    : ['value' => (string) $data, 'unit' => ''];
            }

            $log = $this->importer->receive(
                machine:    $machine,
                rawData:    $rawData,
                parsedData: $parsedData,
                sampleId:   $sampleId,
            );

            return response()->json([
                'status'           => $log->status,
                'log_id'           => $log->id,
                'sample_id'        => $sampleId,
                'parameters_found' => count($parsedData),
                'message'          => "Parsed {$log->status}: {$sampleId}",
            ]);
        } catch (\Throwable $e) {
            Log::error("MachineDataController: Parse error for machine #{$machine->id}: " . $e->getMessage());
            return response()->json(['error' => 'Failed to parse data: ' . $e->getMessage()], 422);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // POST /api/machine/heartbeat
    // ─────────────────────────────────────────────────────────────

    /**
     * Bridge Agent sends periodic heartbeat to mark machine as online.
     */
    public function heartbeat(Request $request): JsonResponse
    {
        $machine = $this->authenticateMachine($request);
        if (!$machine) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        $machine->update(['last_seen_at' => now()]);

        return response()->json([
            'status'    => 'ok',
            'machine'   => $machine->name,
            'server_time' => now()->toIso8601String(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // GET /api/machine/pending/{invoice_id}
    // ─────────────────────────────────────────────────────────────

    /**
     * Result Entry page polls this to check if machine data is ready.
     * Called by Livewire ResultEntryManager via JS polling.
     */
    public function pendingForInvoice(Request $request, int $invoiceId): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $logs = \App\Models\MachineResultLog::where('invoice_id', $invoiceId)
            ->where('company_id', $user->company_id)
            ->where('status', 'matched')
            ->with('machine:id,name,machine_type,brand')
            ->get()
            ->map(fn($log) => [
                'log_id'           => $log->id,
                'machine_name'     => $log->machine?->name,
                'machine_type'     => $log->machine?->machine_type,
                'parameters_count' => count($log->parsed_data ?? []),
                'received_at'      => $log->created_at->diffForHumans(),
            ]);

        return response()->json([
            'has_data' => $logs->isNotEmpty(),
            'logs'     => $logs,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // Private Helpers
    // ─────────────────────────────────────────────────────────────

    private function authenticateMachine(Request $request): ?MachineIntegration
    {
        $token = $request->bearerToken();
        if (!$token) return null;

        return MachineIntegration::where('api_token', $token)
            ->where('is_active', true)
            ->first();
    }
}
