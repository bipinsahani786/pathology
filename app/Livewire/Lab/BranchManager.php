<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Branch;
use Illuminate\Support\Facades\Cache;

class BranchManager extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->authorize('view branches');
    }

    // State variables
    public $searchTerm = '';
    public $branch_id = null; // Explicitly null to prevent PostgreSQL errors
    public $name;
    public $type = 'main_lab';
    public $contact_number;
    public $address;
    public $is_active = true;
    public $email;
    public $password;
    public $isModalOpen = false;

    /**
     * Reset pagination when searching
     */
    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    /**
     * Open modal to create a new branch
     */
    public function create()
    {
        $this->authorize('create branches');
        $this->resetFields();
        $this->isModalOpen = true;
    }

    /**
     * Load existing data and open modal for editing
     */
    public function edit($id)
    {
        $this->authorize('edit branches');
        $this->resetFields();
        $branch = Branch::findOrFail($id);
        
        $this->branch_id = $branch->id;
        $this->name = $branch->name;
        $this->type = $branch->type;
        $this->contact_number = $branch->contact_number;
        $this->address = $branch->address;
        $this->is_active = $branch->is_active;

        // Try to fetch existing branch admin user
        $branchUser = \App\Models\User::where('branch_id', $this->branch_id)
            ->whereHas('roles', fn($q) => $q->where('name', 'branch_admin'))
            ->first();
            
        if ($branchUser) {
            $this->email = $branchUser->email;
        }

        $this->isModalOpen = true;
    }

    /**
     * Validate and save the data to the database
     */
    public function store()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:30',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'email' => 'required|email|max:255',
        ];

        if ($this->branch_id) {
            // Edit: email uniqueness except for the existing user for this branch
            $branchUser = \App\Models\User::where('branch_id', $this->branch_id)->whereHas('roles', fn($q) => $q->where('name', 'branch_admin'))->first();
            if ($branchUser) {
                $rules['email'] .= '|unique:users,email,' . $branchUser->id;
            } else {
                $rules['email'] .= '|unique:users,email';
            }
            if ($this->password) {
                $rules['password'] = 'min:6';
            }
        } else {
            // Pre-validation logic for SaaS Limits limits to avoid user filling form to be rejected
            $company = auth()->user()->company;
            $maxBranches = $company->plan->features['branches'] ?? -1;
            if ($maxBranches != -1) {
                $currentBranchCount = \App\Models\Branch::where('company_id', $company->id)->count();
                if ($currentBranchCount >= $maxBranches) {
                    $this->addError('name', "Plan Limit Reached! Your plan allows only {$maxBranches} branch(es). Upgrade your plan to add more.");
                    return;
                }
            }

            // Create
            $rules['email'] .= '|unique:users,email';
            $rules['password'] = 'required|min:6';
        }

        $this->validate($rules);

        \Illuminate\Support\Facades\DB::transaction(function () {
            // SaaS Plan Enforcement for Branches
            if (!$this->branch_id) {
                $company = auth()->user()->company;
                $maxBranches = $company->plan->features['branches'] ?? -1;
                
                if ($maxBranches != -1) {
                    $currentBranchCount = \App\Models\Branch::where('company_id', $company->id)->count();
                    if ($currentBranchCount >= $maxBranches) {
                        return; // Halt transaction execution
                    }
                }
            }

            // Explicitly separate Create and Update to prevent PostgreSQL 'null id' error
            if ($this->branch_id) {
                $this->authorize('edit branches');
                Branch::where('id', $this->branch_id)->update([
                    'name' => $this->name,
                    'type' => $this->type,
                    'contact_number' => $this->contact_number,
                    'address' => $this->address,
                    'is_active' => $this->is_active,
                ]);

                $branchUser = \App\Models\User::where('branch_id', $this->branch_id)->whereHas('roles', fn($q) => $q->where('name', 'branch_admin'))->first();
                if ($branchUser) {
                    $branchUser->update(['email' => $this->email, 'name' => $this->name . ' Admin']);
                    if ($this->password) {
                        $branchUser->update(['password' => bcrypt($this->password)]);
                    }
                } else {
                    // Create if missing
                    $user = \App\Models\User::create([
                        'name' => $this->name . ' Admin',
                        'email' => $this->email,
                        'password' => bcrypt($this->password ?: 'password123'),
                        'company_id' => auth()->user()->company_id,
                        'branch_id' => $this->branch_id,
                        'is_active' => true,
                    ]);
                    // Auto-assign branch_admin role
                    $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'branch_admin']);
                    $user->assignRole($role);
                }

                session()->flash('message', 'Branch updated successfully.');
            } else {
                $this->authorize('create branches');
                $branch = Branch::create([
                    'company_id' => auth()->user()->company_id,
                    'name' => $this->name,
                    'type' => $this->type,
                    'contact_number' => $this->contact_number,
                    'address' => $this->address,
                    'is_active' => $this->is_active,
                ]);

                // Create branch admin user
                $user = \App\Models\User::create([
                    'name' => $this->name . ' Admin',
                    'email' => $this->email,
                    'password' => bcrypt($this->password),
                    'company_id' => auth()->user()->company_id,
                    'branch_id' => $branch->id,
                    'is_active' => true,
                ]);
                
                // Ensure branch_admin role exists and assign it
                $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'branch_admin']);
                $user->assignRole($role);

                session()->flash('message', 'Branch and Admin account created successfully.');
            }
            Cache::forget("branches_" . auth()->user()->company_id);
            Cache::forget("centers_" . auth()->user()->company_id . "_all");
        });

        $this->closeModal();
    }

    /**
     * Quick toggle for active/inactive status
     */
    public function toggleStatus($id)
    {
        $this->authorize('edit branches');
        $branch = Branch::findOrFail($id);
        $branch->update(['is_active' => !$branch->is_active]);
        Cache::forget("branches_" . auth()->user()->company_id);
        Cache::forget("centers_" . auth()->user()->company_id . "_all");
        session()->flash('message', 'Branch status updated successfully.');
    }

    /**
     * Delete a branch
     */
    public function delete($id)
    {
        $this->authorize('delete branches');
        $branch = Branch::findOrFail($id);
        
        // Also delete the branch admin accounts? Yes, cascade should handle it or manually delete
        \App\Models\User::where('branch_id', $branch->id)->delete();
        $companyId = $branch->company_id;
        $branch->delete();
        Cache::forget("branches_" . $companyId);
        Cache::forget("centers_" . $companyId . "_all");
        session()->flash('message', 'Branch and its accounts deleted successfully.');
    }

    /**
     * Reset form fields
     */
    public function resetFields()
    {
        $this->reset(['branch_id', 'name', 'contact_number', 'address', 'email', 'password']);
        $this->type = 'main_lab';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
    }

    public function render()
    {
        $branches = Branch::where('company_id', auth()->user()->company_id)
            ->where(function($q) {
                $q->where('name', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('contact_number', 'like', '%' . $this->searchTerm . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.lab.branch-manager', [
            'branches' => $branches
        ])->layout('layouts.app', ['title' => 'Manage Branches']);
    }
}