<?php

namespace App\Livewire\Admin;

use App\Models\Company;
use Livewire\Component;
use Livewire\WithPagination;

class LabManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $searchTerm = '';
    public $subscriptionFilter = 'all'; // all, expired, expiring_soon, active

    public $isRegistrationModalOpen = false;

    // Registration Form
    public $labName;
    public $labEmail;
    public $labPhone;
    public $labAddress;
    public $planId;
    public $adminName;
    public $adminEmail;
    public $adminPassword;
    
    // UI State
    public $editingLabId = null;
    public $isViewModalOpen = false;
    public $selectedLab = null;

    protected $rules = [
        'labName' => 'required|string|max:255',
        'labEmail' => 'required|email|unique:companies,email',
        'labPhone' => 'required|string|max:15',
        'labAddress' => 'required|string',
        'planId' => 'required|exists:plans,id',
        'adminName' => 'required|string|max:255',
        'adminEmail' => 'required|email|unique:users,email',
        'adminPassword' => 'required|min:6',
    ];

    public function render()
    {
        $query = Company::with('plan')
            ->where(function ($q) {
                $q->where('name', 'ilike', '%' . $this->searchTerm . '%')
                    ->orWhere('email', 'ilike', '%' . $this->searchTerm . '%');
            });

        // Apply Subscription Filters
        if ($this->subscriptionFilter === 'expired') {
            $query->where('trial_ends_at', '<', now());
        } elseif ($this->subscriptionFilter === 'expiring_soon') {
            $query->whereBetween('trial_ends_at', [now(), now()->addDays(15)]);
        } elseif ($this->subscriptionFilter === 'active') {
            $query->where('trial_ends_at', '>=', now());
        }

        $labs = $query->orderBy('created_at', 'desc')
            ->paginate(10);
            
        // Calculate days left for each lab row
        $labs->getCollection()->transform(function ($lab) {
            if ($lab->trial_ends_at) {
                $lab->days_left = floor(now()->diffInDays($lab->trial_ends_at, false));
            } else {
                $lab->days_left = null;
            }
            return $lab;
        });

        $plans = \App\Models\Plan::where('is_active', true)->get();

        return view('livewire.admin.lab-manager', [
            'labs' => $labs,
            'plans' => $plans
        ])->layout('layouts.app');
    }

    public function openRegistrationModal()
    {
        $this->resetValidation();
        $this->reset(['editingLabId', 'labName', 'labEmail', 'labPhone', 'labAddress', 'planId', 'adminName', 'adminEmail', 'adminPassword']);
        $this->isRegistrationModalOpen = true;
    }

    public function editLab($id)
    {
        $this->resetValidation();
        $this->editingLabId = $id;
        $lab = Company::findOrFail($id);
        
        $this->labName = $lab->name;
        $this->labEmail = $lab->email;
        $this->labPhone = $lab->phone;
        $this->labAddress = $lab->address;
        $this->planId = $lab->plan_id;
        
        // Find the lab admin user
        $admin = \App\Models\User::where('company_id', $id)
            ->role('lab_admin')
            ->first();
            
        if ($admin) {
            $this->adminName = $admin->name;
            $this->adminEmail = $admin->email;
        }

        $this->adminPassword = ''; // Don't show password
        $this->isRegistrationModalOpen = true;
    }

    public function viewDetails($id)
    {
        $this->selectedLab = Company::with(['plan'])->findOrFail($id);
        
        // Add stats or more info if needed
        $this->selectedLab->admin = \App\Models\User::where('company_id', $id)
            ->role('lab_admin')
            ->first();
            
        $this->isViewModalOpen = true;
    }

    public function closeRegistrationModal()
    {
        $this->isRegistrationModalOpen = false;
    }

    public function createLab()
    {
        if ($this->editingLabId) {
            return $this->updateLab();
        }

        $this->validate();

        \Illuminate\Support\Facades\DB::transaction(function () {
            // 1. Create Company
            $plan = \App\Models\Plan::find($this->planId);
            $company = Company::create([
                'name' => $this->labName,
                'email' => $this->labEmail,
                'phone' => $this->labPhone,
                'address' => $this->labAddress,
                'plan_id' => $this->planId,
                'status' => 'active',
                'trial_ends_at' => now()->addDays($plan->duration_in_days ?? 30),
            ]);

            // 2. Create Default Branch
            $branch = \App\Models\Branch::create([
                'company_id' => $company->id,
                'name' => 'Main Center',
                'address' => $this->labAddress,
            ]);

            // 3. Create Default User (Lab Admin)
            $user = \App\Models\User::create([
                'name' => $this->adminName,
                'email' => $this->adminEmail,
                'password' => \Illuminate\Support\Facades\Hash::make($this->adminPassword),
                'company_id' => $company->id,
                'branch_id' => $branch->id,
                'is_active' => true,
            ]);

            // Assign Role
            $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'lab_admin']);
            $user->assignRole($role);
        });

        session()->flash('success', 'Lab created successfully with Admin credentials!');
        $this->closeRegistrationModal();
    }

    public function updateLab()
    {
        $this->validate([
            'labName' => 'required|string|max:255',
            'labEmail' => 'required|email|unique:companies,email,' . $this->editingLabId,
            'labPhone' => 'required|string|max:15',
            'labAddress' => 'required|string',
            'planId' => 'required|exists:plans,id',
            'adminName' => 'required|string|max:255',
            'adminEmail' => 'required|email|unique:users,email,' . (\App\Models\User::where('company_id', $this->editingLabId)->role('lab_admin')->first()->id ?? 0),
            'adminPassword' => 'nullable|min:6',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () {
            $company = Company::findOrFail($this->editingLabId);
            $oldPlanId = $company->plan_id;

            $company->update([
                'name' => $this->labName,
                'email' => $this->labEmail,
                'phone' => $this->labPhone,
                'address' => $this->labAddress,
                'plan_id' => $this->planId,
            ]);

            // If plan changed, maybe extend trial?
            if ($oldPlanId != $this->planId) {
                $plan = \App\Models\Plan::find($this->planId);
                $company->update([
                    'trial_ends_at' => now()->addDays($plan->duration_in_days ?? 30)
                ]);
            }

            // Update Admin User
            $admin = \App\Models\User::where('company_id', $this->editingLabId)
                ->role('lab_admin')
                ->first();
                
            if ($admin) {
                $userData = [
                    'name' => $this->adminName,
                    'email' => $this->adminEmail,
                ];
                if ($this->adminPassword) {
                    $userData['password'] = \Illuminate\Support\Facades\Hash::make($this->adminPassword);
                }
                $admin->update($userData);
            }
        });

        session()->flash('success', 'Lab details updated successfully!');
        $this->closeRegistrationModal();
    }

    public function toggleStatus($id)
    {
        $lab = Company::findOrFail($id);
        $lab->update(['status' => $lab->status === 'active' ? 'inactive' : 'active']);
        session()->flash('success', 'Lab status updated successfully.');
    }
}
