<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CollectionCenter;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CollectionCenterManager extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->authorize('view collection_centers');
    }

    // State variables
    public $searchTerm = '';
    public $center_id = null; 
    public $user_id = null;
    
    // Center Fields
    public $name;
    public $center_code;
    public $address;
    public $branch_id;
    public $is_active = true;

    // User Fields
    public $phone;
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
     * Open modal to create a new center
     */
    public function create()
    {
        $this->authorize('create collection_centers');
        $this->resetFields();
        $this->isModalOpen = true;
    }

    /**
     * Load existing data and open modal for editing
     */
    public function edit($id)
    {
        $this->authorize('edit collection_centers');
        $this->resetFields();
        $center = CollectionCenter::with('user')->findOrFail($id);
        
        $this->center_id = $center->id;
        $this->name = $center->name;
        $this->center_code = $center->center_code;
        $this->address = $center->address;
        $this->branch_id = $center->branch_id;
        $this->is_active = $center->is_active;

        if ($center->user) {
            $this->user_id = $center->user->id;
            $this->phone = $center->user->phone;
            $this->email = $center->user->email;
        }

        $this->isModalOpen = true;
    }

    /**
     * Validate and save the data to the database
     */
    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'center_code' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'branch_id' => 'required|exists:branches,id',
            'phone' => [
                'nullable',
                'numeric',
                'digits:10',
                Rule::unique('users', 'phone')->ignore($this->user_id),
            ],
            'email' => [
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($this->user_id),
            ],
            'password' => 'nullable|min:6',
        ]);

        DB::beginTransaction();
        try {
            $company = auth()->user()->company;
            $companyId = $company->id;

            // SaaS Plan Enforcement for Collection Centers
            if (!$this->center_id) {
                $maxCenters = $company->plan->features['collection_centers'] ?? -1;
                if ($maxCenters != -1) {
                    $currentCentersCount = CollectionCenter::where('company_id', $companyId)->count();
                    if ($currentCentersCount >= $maxCenters) {
                        $this->addError('name', "Plan Limit Reached! Your plan allows only {$maxCenters} collection center(s). Upgrade your plan to add more.");
                        return;
                    }
                }
            }

            // 1. Manage User (Login Account)
            if ($this->user_id) {
                // Update existing user
                $user = User::findOrFail($this->user_id);
                $userData = [
                    'name' => $this->name,
                    'phone' => $this->phone,
                    'email' => $this->email,
                    'is_active' => $this->is_active,
                ];
                if ($this->password) {
                    $userData['password'] = Hash::make($this->password);
                }
                $user->update($userData);
            } elseif ($this->phone || $this->email) {
                // Create new user if phone/email provided
                $user = User::create([
                    'company_id' => $companyId,
                    'name' => $this->name,
                    'phone' => $this->phone,
                    'email' => $this->email ?: null,
                    'password' => Hash::make($this->password ?? $this->phone ?? '12345678'),
                    'is_active' => $this->is_active,
                ]);

                // Robust role assignment: find a role that is/was collection_center
                $ccRole = \Spatie\Permission\Models\Role::where('name', 'collection_center')
                    ->orWhere('name', 'like', '%collection%')
                    ->first();
                
                if ($ccRole) {
                    $user->assignRole($ccRole->name);
                } else {
                    $user->assignRole('collection_center'); // Fallback
                }
                
                $this->user_id = $user->id;
            }

            // 2. Manage Collection Center
            if ($this->center_id) {
                $this->authorize('edit collection_centers');
                CollectionCenter::where('id', $this->center_id)->update([
                    'name' => $this->name,
                    'center_code' => $this->center_code,
                    'address' => $this->address,
                    'branch_id' => $this->branch_id,
                    'is_active' => $this->is_active,
                    'user_id' => $this->user_id,
                ]);
                session()->flash('message', 'Collection Center updated successfully.');
            } else {
                $this->authorize('create collection_centers');
                $center = CollectionCenter::create([
                    'company_id' => $companyId,
                    'user_id' => $this->user_id,
                    'branch_id' => $this->branch_id,
                    'name' => $this->name,
                    'center_code' => $this->center_code,
                    'address' => $this->address,
                    'is_active' => $this->is_active,
                ]);
                $this->center_id = $center->id;
                session()->flash('message', 'Collection Center created successfully.');
            }

            // 3. Link back center ID to user
            if ($this->user_id) {
                User::where('id', $this->user_id)->update(['collection_center_id' => $this->center_id]);
            }

            DB::commit();
            $this->closeModal();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Quick toggle for active/inactive status
     */
    public function toggleStatus($id)
    {
        $this->authorize('edit collection_centers');
        $center = CollectionCenter::findOrFail($id);
        $center->update(['is_active' => !$center->is_active]);
        
        session()->flash('message', 'Status updated successfully.');
    }

    /**
     * Delete a center
     */
    public function delete($id)
    {
        $this->authorize('delete collection_centers');
        CollectionCenter::findOrFail($id)->delete();
        session()->flash('message', 'Collection Center deleted successfully.');
    }

    /**
     * Reset form fields
     */
    public function resetFields()
    {
        $this->reset(['center_id', 'user_id', 'name', 'center_code', 'address', 'phone', 'email', 'password']);
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
        $user = auth()->user();
        $companyId = $user->company_id;
        $activeBranchId = session('active_branch_id', 'all');
        
        $roles = $user->roles->pluck('name')->toArray();
        $isGlobalAdmin = $user->hasAnyRole(['lab_admin', 'super_admin']) || collect($roles)->contains(fn($r) => str_ends_with($r, '_admin') || str_ends_with($r, '_super_admin') || str_contains(strtolower($r), 'admin'));
        
        $myBranchId = $isGlobalAdmin 
            ? ($activeBranchId === 'all' ? null : $activeBranchId) 
            : $user->branch_id;

        $centers = CollectionCenter::with('user')->where('company_id', $companyId)
            ->when($myBranchId, fn($q) => $q->where('branch_id', $myBranchId))
            ->where(function($q) {
                $q->where('name', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('center_code', 'like', '%' . $this->searchTerm . '%')
                  ->orWhereHas('user', function($qu) {
                      $qu->where('phone', 'like', '%' . $this->searchTerm . '%');
                  });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.lab.collection-center-manager', [
            'centers' => $centers,
            'branches' => \App\Models\Branch::where('company_id', $companyId)
                ->when($myBranchId, fn($q) => $q->where('id', $myBranchId))
                ->get()
        ])->layout('layouts.app', ['title' => 'Manage Collection Centers']);
    }
}