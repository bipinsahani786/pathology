<?php

namespace App\Livewire\Lab;

use App\Models\AgentProfile;
use App\Models\User;
use App\Services\Import\BulkImportService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class AgentManager extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->authorize('view agents');
    }

    // State variables
    public $searchTerm = '';

    public $user_id = null; // Tracks the User ID for editing

    // User Table Fields
    public $name;

    public $phone;

    public $email;

    public $password;

    // Agent Profile Fields
    public $agency_name;

    public $commission_percentage = 0;

    public $isModalOpen = false;

    // Bulk Import Variables
    public $importFile;

    public $isImportModalOpen = false;

    public $importSummary = null;

    /**
     * Reset pagination when searching
     */
    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    /**
     * Open modal to create a new agent
     */
    public function create()
    {
        $this->authorize('create agents');
        $this->resetFields();
        $this->isModalOpen = true;
    }

    /**
     * Load existing agent data and open modal for editing
     */
    public function edit($id)
    {
        $this->authorize('edit agents');
        $this->resetFields();

        // Eager load the profile to avoid N+1 query issues
        $user = User::where('company_id', auth()->user()->company_id)->with('agentProfile')->findOrFail($id);

        $this->user_id = $user->id;
        $this->name = $user->name;
        $this->phone = $user->phone;
        $this->email = $user->email;

        if ($user->agentProfile) {
            $this->agency_name = $user->agentProfile->agency_name;
            $this->commission_percentage = $user->agentProfile->commission_percentage;
        }

        $this->isModalOpen = true;
    }

    /**
     * Validate and save the agent data to both tables
     */
    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'phone' => [
                'nullable',
                'numeric',
                'digits:10',
            ],
            'email' => [
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($this->user_id),
            ],
            'agency_name' => 'nullable|string|max:255',
            'commission_percentage' => 'required|numeric|min:0|max:100',
            'password' => $this->user_id ? 'nullable|min:6' : 'nullable|min:6',
        ]);

        DB::beginTransaction();
        try {
            if ($this->user_id) {
                $this->authorize('edit agents');
                // UPDATE EXISTING AGENT
                $user = User::where('company_id', auth()->user()->company_id)->findOrFail($this->user_id);
                $updateData = [
                    'name' => $this->name,
                    'phone' => $this->phone,
                    'email' => $this->email,
                ];

                if ($this->password) {
                    $updateData['password'] = Hash::make($this->password);
                }

                $user->update($updateData);

                AgentProfile::where('user_id', $this->user_id)->update([
                    'agency_name' => $this->agency_name,
                    'commission_percentage' => $this->commission_percentage,
                ]);

                session()->flash('message', 'Agent details updated successfully.');
            } else {
                $this->authorize('create agents');
                $company = auth()->user()->company;

                // SaaS Plan Enforcement for Agents
                $maxAgents = $company->plan->features['agents'] ?? -1;
                if ($maxAgents != -1) {
                    $currentAgentsCount = \App\Models\AgentProfile::where('company_id', $company->id)->count();
                    if ($currentAgentsCount >= $maxAgents) {
                        $this->addError('name', "Plan Limit Reached! Your plan allows only {$maxAgents} marketing agent(s). Upgrade your plan to add more.");

                        return;
                    }
                }

                $companyId = $company->id;
                // CREATE NEW AGENT

                $activeBranchId = session('active_branch_id', 'all');
                $roles = auth()->user()->roles->pluck('name')->toArray();
                $isGlobalAdmin = (auth()->user()->hasAnyRole(['lab_admin', 'super_admin']) ||
                                 collect($roles)->contains(fn ($r) => str_ends_with($r, '_admin') || str_ends_with($r, '_super_admin') || str_contains(strtolower($r), 'admin')))
                                 && ! auth()->user()->hasRole('branch_admin');

                $myBranchId = $isGlobalAdmin
                    ? ($activeBranchId === 'all' ? null : $activeBranchId)
                    : auth()->user()->branch_id;

                // 1. Create the base User record
                $user = User::create([
                    'company_id' => $companyId,
                    'branch_id' => $myBranchId,
                    'name' => $this->name,
                    'phone' => $this->phone,
                    'email' => $this->email ?: null,
                    'password' => Hash::make($this->password ?? $this->phone ?? 'password123'),
                    'is_active' => true,
                ]);

                // 2. Create the Agent Profile record
                AgentProfile::create([
                    'company_id' => $companyId,
                    'user_id' => $user->id,
                    'agency_name' => $this->agency_name,
                    'commission_percentage' => $this->commission_percentage,
                ]);

                // Assign Role
                $user->assignRole('agent');

                session()->flash('message', 'New referral agent added successfully.');
            }

            DB::commit();
            $this->closeModal();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error saving agent: '.$e->getMessage());
        }
    }

    /**
     * Toggle agent account status (Active/Inactive)
     */
    public function toggleStatus($id)
    {
        $this->authorize('edit agents');
        $user = User::where('company_id', auth()->user()->company_id)->findOrFail($id);
        $user->is_active = ! $user->is_active;
        $user->save();

        session()->flash('message', 'Agent status updated to '.($user->is_active ? 'Active' : 'Inactive'));
    }

    public function delete($id)
    {
        $this->authorize('delete agents');

        if (\App\Models\Invoice::where('referred_by_agent_id', $id)->exists()) {
            session()->flash('error', 'Cannot delete agent as they are linked to existing invoices.');
            return;
        }

        User::where('company_id', auth()->user()->company_id)->findOrFail($id)->delete();
        session()->flash('message', 'Agent deleted successfully.');
    }

    /**
     * Reset form fields
     */
    public function resetFields()
    {
        $this->reset(['user_id', 'name', 'phone', 'email', 'agency_name', 'password']);
        $this->commission_percentage = 0;
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
    }

    /**
     * Open Bulk Import Modal
     */
    public function openImportModal()
    {
        $this->authorize('create agents');
        $this->reset(['importFile', 'importSummary']);
        $this->resetValidation();
        $this->isImportModalOpen = true;
    }

    /**
     * Close Bulk Import Modal
     */
    public function closeImportModal()
    {
        $this->isImportModalOpen = false;
        $this->reset(['importFile', 'importSummary']);
        $this->resetValidation();
    }

    /**
     * Download sample Excel template for agents
     */
    public function downloadSampleTemplate(BulkImportService $importService)
    {
        return $importService->downloadSampleTemplate('agents');
    }

    /**
     * Process bulk Excel/CSV import for agents
     */
    public function processBulkImport(BulkImportService $importService)
    {
        $this->authorize('create agents');

        try {
            $this->validate([
                'importFile' => 'required|file|max:10240',
            ], [
                'importFile.required' => 'Please select an Excel or CSV file to import.',
                'importFile.max' => 'File size cannot exceed 10MB.',
            ]);
        } catch (\League\Flysystem\UnableToRetrieveMetadata | \League\Flysystem\FilesystemException $e) {
            $this->importFile = null;
            $this->importSummary = [
                'total' => 0,
                'success' => 0,
                'skipped' => 0,
                'errors' => ['Uploaded file expired or could not be found. Please browse and select your Excel file again.'],
            ];
            return;
        }

        $filePath = null;
        try {
            if (method_exists($this->importFile, 'getRealPath') && $this->importFile->getRealPath() && file_exists($this->importFile->getRealPath())) {
                $filePath = $this->importFile->getRealPath();
            }
        } catch (\Throwable $e) {
            $filePath = null;
        }

        if (! $filePath || ! file_exists($filePath)) {
            try {
                $fn = $this->importFile->getFilename();
                if (file_exists(storage_path('app/private/livewire-tmp/' . $fn))) {
                    $filePath = storage_path('app/private/livewire-tmp/' . $fn);
                } elseif (file_exists(storage_path('app/livewire-tmp/' . $fn))) {
                    $filePath = storage_path('app/livewire-tmp/' . $fn);
                }
            } catch (\Throwable $e) {
                $filePath = null;
            }
        }

        if (! $filePath || ! file_exists($filePath)) {
            $this->importFile = null;
            $this->importSummary = [
                'total' => 0,
                'success' => 0,
                'skipped' => 0,
                'errors' => ['Unable to read uploaded file. Please select your file again.'],
            ];
            return;
        }

        $user = auth()->user();
        $companyId = $user->company_id;

        $activeBranchId = session('active_branch_id', 'all');
        $roles = $user->roles->pluck('name')->toArray();
        $isGlobalAdmin = ($user->hasAnyRole(['lab_admin', 'super_admin']) ||
                         collect($roles)->contains(fn ($r) => str_ends_with($r, '_admin') || str_ends_with($r, '_super_admin') || str_contains(strtolower($r), 'admin')))
                         && ! $user->hasRole('branch_admin');

        $myBranchId = $isGlobalAdmin
            ? ($activeBranchId === 'all' ? null : $activeBranchId)
            : $user->branch_id;

        $this->importSummary = $importService->importAgents($filePath, $companyId, $myBranchId);
        $this->importFile = null;

        if ($this->importSummary['success'] > 0) {
            session()->flash('message', "Successfully imported {$this->importSummary['success']} referral agent(s).");
        }
        if ($this->importSummary['skipped'] > 0 && $this->importSummary['success'] === 0) {
            session()->flash('error', "Import completed with errors. Please check the summary above.");
        }
    }

    public function render()
    {
        $companyId = auth()->user()->company_id;
        $restrictAccess = \App\Models\Configuration::getFor('restrict_branch_access', '1') === '1';
        $activeBranchId = session('active_branch_id', 'all');

        $roles = auth()->user()->roles->pluck('name')->toArray();
        $isGlobalAdmin = (auth()->user()->hasAnyRole(['lab_admin', 'super_admin']) ||
                         collect($roles)->contains(fn ($r) => str_ends_with($r, '_admin') || str_ends_with($r, '_super_admin') || str_contains(strtolower($r), 'admin')))
                         && ! auth()->user()->hasRole('branch_admin');

        $myBranchId = null;
        if ($isGlobalAdmin) {
            $myBranchId = ($activeBranchId === 'all' ? null : $activeBranchId);
        } else {
            $myBranchId = auth()->user()->branch_id;
        }

        // If strict branch access is enabled, force myBranchId if it was null AND user is NOT a global admin
        if ($restrictAccess && ! $myBranchId && ! $isGlobalAdmin) {
            $myBranchId = auth()->user()->branch_id;
        }

        $shareAgents = \App\Models\Configuration::getFor('branch_share_agents', '1') === '1';

        // Fetch only users who have an AgentProfile attached to the current company
        $query = User::whereHas('agentProfile', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        });

        if ($myBranchId && ! $shareAgents) {
            $query->where('branch_id', $myBranchId);
        }

        $agents = $query->with('agentProfile')
            ->where(function ($q) {
                $q->where('name', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('phone', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhereHas('agentProfile', function ($query2) {
                        $query2->where('agency_name', 'ilike', '%'.$this->searchTerm.'%');
                    });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.lab.agent-manager', [
            'agents' => $agents,
        ])->layout('layouts.app', ['title' => 'Referral Agents']);
    }
}
