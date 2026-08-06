<?php

namespace App\Livewire\Lab;

use App\Models\MachineIntegration;
use App\Models\MachineResultLog;
use App\Services\MachineIntegration\MachineParserFactory;
use App\Services\MachineIntegration\ResultImporter;
use Livewire\Component;

class MachineManager extends Component
{
    // ─────────────────────────────────────────
    // List state
    // ─────────────────────────────────────────
    public $machines = [];

    // ─────────────────────────────────────────
    // Form state
    // ─────────────────────────────────────────
    public $showForm       = false;
    public $editingId      = null;

    public $name           = '';
    public $brand          = '';
    public $machine_type   = 'biochemistry';
    public $connection_type = 'serial';
    public $port_or_ip     = '';
    public $baud_rate      = 9600;
    public $tcp_port       = 4001;
    public $protocol       = 'astm';
    public $notes          = '';
    public $is_active      = true;

    // Parameter mapping (JSON text area)
    public $test_mapping_raw = '';

    // ─────────────────────────────────────────
    // Token reveal
    // ─────────────────────────────────────────
    public $revealedTokenId = null;

    // ─────────────────────────────────────────
    // Feedback
    // ─────────────────────────────────────────
    public $savedId        = null;
    public $deletedName    = null;

    // ─────────────────────────────────────────
    // Simulator
    // ─────────────────────────────────────────
    public $simulatorMachineId = null;
    public $simulatorSampleId  = '';
    public $simulatorTestType  = 'lft';
    public $simulatorResult    = null;
    public $showSimulator      = false;

    // ─────────────────────────────────────────
    // Logs modal
    // ─────────────────────────────────────────
    public $showLogs       = false;
    public $logsForMachine = null;
    public $logs           = [];

    // ─────────────────────────────────────────
    // Mount
    // ─────────────────────────────────────────
    public function mount()
    {
        $this->loadMachines();
    }

    private function loadMachines()
    {
        $this->machines = MachineIntegration::where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    // ─────────────────────────────────────────
    // Form Open / Close
    // ─────────────────────────────────────────
    public function openCreate()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id)
    {
        $machine = MachineIntegration::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        $this->editingId        = $machine->id;
        $this->name             = $machine->name;
        $this->brand            = $machine->brand ?? '';
        $this->machine_type     = $machine->machine_type;
        $this->connection_type  = $machine->connection_type;
        $this->port_or_ip       = $machine->port_or_ip ?? '';
        $this->baud_rate        = $machine->baud_rate ?? 9600;
        $this->tcp_port         = $machine->tcp_port ?? 4001;
        $this->protocol         = $machine->protocol;
        $this->notes            = $machine->notes ?? '';
        $this->is_active        = $machine->is_active;
        $this->test_mapping_raw = $machine->test_mapping
            ? json_encode($machine->test_mapping, JSON_PRETTY_PRINT)
            : '';

        $this->showForm = true;
    }

    public function closeForm()
    {
        $this->showForm = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->editingId        = null;
        $this->name             = '';
        $this->brand            = '';
        $this->machine_type     = 'biochemistry';
        $this->connection_type  = 'serial';
        $this->port_or_ip       = '';
        $this->baud_rate        = 9600;
        $this->tcp_port         = 4001;
        $this->protocol         = 'astm';
        $this->notes            = '';
        $this->is_active        = true;
        $this->test_mapping_raw = '';
    }

    // ─────────────────────────────────────────
    // Save
    // ─────────────────────────────────────────
    public function save()
    {
        $this->validate([
            'name'            => 'required|string|max:100',
            'machine_type'    => 'required|in:biochemistry,hematology,electrolyte,other',
            'connection_type' => 'required|in:serial,tcp,http_push',
            'protocol'        => 'required|in:astm,hl7,custom',
            'baud_rate'       => 'nullable|integer',
            'tcp_port'        => 'nullable|integer',
        ]);

        // Parse test mapping JSON
        $testMapping = null;
        if (!empty(trim($this->test_mapping_raw))) {
            $testMapping = json_decode($this->test_mapping_raw, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->addError('test_mapping_raw', 'Invalid JSON format for parameter mapping.');
                return;
            }
        }

        $data = [
            'company_id'      => auth()->user()->company_id,
            'name'            => $this->name,
            'brand'           => $this->brand ?: null,
            'machine_type'    => $this->machine_type,
            'connection_type' => $this->connection_type,
            'port_or_ip'      => $this->port_or_ip ?: null,
            'baud_rate'       => $this->baud_rate,
            'tcp_port'        => $this->tcp_port ?: null,
            'protocol'        => $this->protocol,
            'notes'           => $this->notes ?: null,
            'is_active'       => $this->is_active,
            'test_mapping'    => $testMapping,
        ];

        if ($this->editingId) {
            $machine = MachineIntegration::where('company_id', auth()->user()->company_id)
                ->findOrFail($this->editingId);
            $machine->update($data);
            $this->savedId = $machine->id;
        } else {
            $machine = MachineIntegration::create($data);
            $this->savedId = $machine->id;
        }

        $this->closeForm();
        $this->loadMachines();

        $this->dispatch('notify', type: 'success', message: 'Machine saved successfully!');
    }

    // ─────────────────────────────────────────
    // Delete
    // ─────────────────────────────────────────
    public function delete(int $id)
    {
        $machine = MachineIntegration::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        $this->deletedName = $machine->name;
        $machine->delete();
        $this->loadMachines();

        $this->dispatch('notify', type: 'success', message: "{$this->deletedName} deleted.");
    }

    // ─────────────────────────────────────────
    // Regenerate Token
    // ─────────────────────────────────────────
    public function regenerateToken(int $id)
    {
        $machine = MachineIntegration::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        $machine->update(['api_token' => MachineIntegration::generateToken()]);
        $this->loadMachines();
        $this->revealedTokenId = $id;

        $this->dispatch('notify', type: 'warning', message: 'Token regenerated. Update your Bridge Agent!');
    }

    public function revealToken(int $id)
    {
        $this->revealedTokenId = ($this->revealedTokenId === $id) ? null : $id;
    }

    // ─────────────────────────────────────────
    // Simulator
    // ─────────────────────────────────────────
    public function openSimulator(int $machineId)
    {
        $this->simulatorMachineId = $machineId;
        $this->simulatorSampleId  = 'LAB' . now()->format('ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        $this->simulatorResult    = null;
        $this->showSimulator      = true;
    }

    public function runSimulator()
    {
        $machine = MachineIntegration::where('company_id', auth()->user()->company_id)
            ->find($this->simulatorMachineId);

        if (!$machine) return;

        // Generate fake data
        $rawData  = MachineParserFactory::generateSimulated($machine, $this->simulatorSampleId, $this->simulatorTestType);
        $parsed   = MachineParserFactory::parse($machine, $rawData);

        // Save through importer
        $importer = new ResultImporter();
        $log = $importer->receive(
            machine:    $machine,
            rawData:    $rawData,
            parsedData: collect($parsed['results'])->mapWithKeys(
                fn($v, $k) => [$k => is_array($v) ? $v : ['value' => $v, 'unit' => '']]
            )->toArray(),
            sampleId:   $this->simulatorSampleId,
        );

        $this->simulatorResult = [
            'log_id'      => $log->id,
            'status'      => $log->status,
            'sample_id'   => $this->simulatorSampleId,
            'raw_data'    => $rawData,
            'parsed'      => $parsed['results'],
            'params_count'=> count($parsed['results']),
        ];
    }

    public function closeSimulator()
    {
        $this->showSimulator   = false;
        $this->simulatorResult = null;
    }

    // ─────────────────────────────────────────
    // Logs
    // ─────────────────────────────────────────
    public function openLogs(int $machineId)
    {
        $this->logsForMachine = MachineIntegration::where('company_id', auth()->user()->company_id)
            ->find($machineId);

        $this->logs = MachineResultLog::where('machine_integration_id', $machineId)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->toArray();

        $this->showLogs = true;
    }

    public function closeLogs()
    {
        $this->showLogs = false;
        $this->logs     = [];
    }

    // ─────────────────────────────────────────
    // Render
    // ─────────────────────────────────────────
    public function render()
    {
        return view('livewire.lab.machine-manager')
            ->layout('layouts.app', ['title' => 'Machine Integration']);
    }
}
