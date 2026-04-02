<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\PatientProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule; // Required for Postgres-safe unique validation

class PatientManager extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';
    
    public function mount()
    {
        $this->authorize('view patients');
    }

    // State variables
    public $searchTerm = '';
    public $user_id = null; // We track the User ID for editing
    
    // User Table Fields
    public $name;
    public $phone;
    public $email;
    
    // Patient Profile Fields
    public $age;
    public $age_type = 'Years';
    public $gender = 'Male';
    public $blood_group;
    public $address;

    public $isModalOpen = false;

    /**
     * Reset pagination when searching
     */
    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    /**
     * Open modal to create a new patient
     */
    public function create()
    {
        $this->authorize('create patients');
        $this->resetFields();
        $this->isModalOpen = true;
    }

    /**
     * Load existing patient data and open modal for editing
     */
    public function edit($id)
    {
        $this->authorize('edit patients');
        $this->resetFields();
        
        // Eager load the profile to avoid N+1 query issues
        $user = User::with('patientProfile')->findOrFail($id);
        
        $this->user_id = $user->id;
        $this->name = $user->name;
        $this->phone = $user->phone;
        $this->email = $user->email;
        
        if ($user->patientProfile) {
            $this->age = $user->patientProfile->age;
            $this->age_type = $user->patientProfile->age_type;
            $this->gender = $user->patientProfile->gender;
            $this->blood_group = $user->patientProfile->blood_group;
            $this->address = $user->patientProfile->address;
        }

        $this->isModalOpen = true;
    }

    /**
     * Validate and save the patient data to both tables
     */
    public function store()
    {
        // FIX: Using Rule::unique()->ignore() handles null IDs securely for Postgres
        $this->validate([
            'name' => 'required|string|max:255',
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
            'age' => 'required|numeric|min:1|max:150',
            'gender' => 'required|in:Male,Female,Other',
            'blood_group' => 'nullable|string|max:5',
        ]);

        DB::beginTransaction();
        try {
            if ($this->user_id) {
                $this->authorize('edit patients');
            } else {
                $this->authorize('create patients');
            }

            $companyId = auth()->user()->company_id;

            if ($this->user_id) {
                // UPDATE EXISTING PATIENT
                $user = User::findOrFail($this->user_id);
                $user->update([
                    'name' => $this->name,
                    'phone' => $this->phone,
                    'email' => $this->email,
                ]);

                PatientProfile::where('user_id', $this->user_id)->update([
                    'age' => $this->age,
                    'age_type' => $this->age_type,
                    'gender' => $this->gender,
                    'blood_group' => $this->blood_group,
                    'address' => $this->address,
                ]);

                session()->flash('message', 'Patient details updated successfully.');
            } else {
                // CREATE NEW PATIENT
                
                // 1. Create the User record (Allows them to log in later)
                $activeBranchId = session('active_branch_id', 'all');
                $myBranchId = auth()->user()->hasRole('lab_admin') || auth()->user()->hasRole('super_admin') 
                    ? ($activeBranchId === 'all' ? null : $activeBranchId) 
                    : auth()->user()->branch_id;

                $user = User::create([
                    'name' => $this->name,
                    'phone' => $this->phone,
                    'email' => $this->email ?: null, 
                    'password' => Hash::make($this->phone ?? '12345678'), 
                    'is_active' => true,
                    'company_id' => $companyId,
                    'branch_id' => $myBranchId,
                ]);

                // 2. Generate a unique Patient ID (e.g., PAT-2603-0001)
                $patientIdString = 'PAT-' . date('ym') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

                // 3. Create the Patient Profile record
                PatientProfile::create([
                    'company_id' => $companyId,
                    'user_id' => $user->id,
                    'patient_id_string' => $patientIdString,
                    'age' => $this->age,
                    'age_type' => $this->age_type,
                    'gender' => $this->gender,
                    'blood_group' => $this->blood_group,
                    'address' => $this->address,
                ]);

                // Assign Role
                $user->assignRole('patient');

                session()->flash('message', 'New patient registered successfully.');
            }

            DB::commit();
            $this->closeModal();
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error saving patient: ' . $e->getMessage());
        }
    }

    /**
     * Delete a patient (Will cascade delete their profile)
     */
    public function delete($id)
    {
        $this->authorize('delete patients');
        // Because of 'cascadeOnDelete' in migration, deleting the user deletes the profile too.
        User::findOrFail($id)->delete();
        session()->flash('message', 'Patient deleted successfully.');
    }

    /**
     * Reset form fields
     */
    public function resetFields()
    {
        $this->reset(['user_id', 'name', 'phone', 'email', 'age', 'blood_group', 'address']);
        $this->age_type = 'Years';
        $this->gender = 'Male';
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
    }

    public function render()
    {
        $companyId = auth()->user()->company_id;
        $activeBranchId = session('active_branch_id', 'all');
        $myBranchId = auth()->user()->hasRole('lab_admin') || auth()->user()->hasRole('super_admin') 
            ? ($activeBranchId === 'all' ? null : $activeBranchId) 
            : auth()->user()->branch_id;
        
        $sharePatients = \App\Models\Configuration::getFor('branch_share_patients', '1') === '1';

        // Fetch only users who have a PatientProfile attached to the current company
        $patients = User::whereHas('patientProfile', function($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->when($myBranchId && !$sharePatients, fn($q) => $q->where('branch_id', $myBranchId))
            ->with(['patientProfile', 'activeMembership.membership']) // Eager load to prevent slow queries
            ->where(function($q) {
                $q->where('name', 'ilike', '%' . $this->searchTerm . '%')
                  ->orWhere('phone', 'ilike', '%' . $this->searchTerm . '%')
                  ->orWhereHas('patientProfile', function($query2) {
                      $query2->where('patient_id_string', 'ilike', '%' . $this->searchTerm . '%');
                  });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.lab.patient-manager', [
            'patients' => $patients
        ])->layout('layouts.app', ['title' => 'Patient Master']);
    }
}