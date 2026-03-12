<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\DoctorProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule; // Required for Postgres-safe unique validation

class DoctorManager extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';

    // State variables
    public $searchTerm = '';
    public $user_id = null; // Tracks the User ID for editing
    
    // User Table Fields
    public $name;
    public $phone;
    public $email;
    
    // Doctor Profile Fields
    public $specialization;
    public $clinic_name;
    public $commission_percentage = 0;

    public $isModalOpen = false;

    /**
     * Reset pagination when searching
     */
    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    /**
     * Open modal to create a new doctor
     */
    public function create()
    {
        $this->resetFields();
        $this->isModalOpen = true;
    }

    /**
     * Load existing doctor data and open modal for editing
     */
    public function edit($id)
    {
        $this->resetFields();
        
        // Eager load the profile to avoid N+1 query issues
        $user = User::with('doctorProfile')->findOrFail($id);
        
        $this->user_id = $user->id;
        $this->name = $user->name;
        $this->phone = $user->phone;
        $this->email = $user->email;
        
        if ($user->doctorProfile) {
            $this->specialization = $user->doctorProfile->specialization;
            $this->clinic_name = $user->doctorProfile->clinic_name;
            $this->commission_percentage = $user->doctorProfile->commission_percentage;
        }

        $this->isModalOpen = true;
    }

    /**
     * Validate and save the doctor data to both tables
     */
    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'numeric',
                'digits:10',
                Rule::unique('users', 'phone')->ignore($this->user_id),
            ],
            'email' => [
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($this->user_id),
            ],
            'specialization' => 'nullable|string|max:255',
            'clinic_name' => 'nullable|string|max:255',
            'commission_percentage' => 'required|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            $companyId = auth()->user()->company_id;

            if ($this->user_id) {
                // UPDATE EXISTING DOCTOR
                $user = User::findOrFail($this->user_id);
                $user->update([
                    'name' => $this->name,
                    'phone' => $this->phone,
                    'email' => $this->email,
                ]);

                DoctorProfile::where('user_id', $this->user_id)->update([
                    'specialization' => $this->specialization,
                    'clinic_name' => $this->clinic_name,
                    'commission_percentage' => $this->commission_percentage,
                ]);

                session()->flash('message', 'Doctor details updated successfully.');
            } else {
                // CREATE NEW DOCTOR
                
                // 1. Create the base User record (Prefixing Dr. if not provided can be done here)
                $finalName = str_starts_with(strtolower($this->name), 'dr') ? $this->name : 'Dr. ' . $this->name;

                $user = User::create([
                    'name' => $finalName,
                    'phone' => $this->phone,
                    'email' => $this->email ?? $this->phone . '@doctor.local', 
                    'password' => Hash::make($this->phone), 
                    'is_active' => true,
                ]);

                // 2. Create the Doctor Profile record
                DoctorProfile::create([
                    'company_id' => $companyId,
                    'user_id' => $user->id,
                    'specialization' => $this->specialization,
                    'clinic_name' => $this->clinic_name,
                    'commission_percentage' => $this->commission_percentage,
                ]);

                // Optional: $user->assignRole('doctor'); if using Spatie

                session()->flash('message', 'New referring doctor added successfully.');
            }

            DB::commit();
            $this->closeModal();
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error saving doctor: ' . $e->getMessage());
        }
    }

    /**
     * Delete a doctor (Will cascade delete their profile)
     */
    public function delete($id)
    {
        User::findOrFail($id)->delete();
        session()->flash('message', 'Doctor deleted successfully.');
    }

    /**
     * Reset form fields
     */
    public function resetFields()
    {
        $this->reset(['user_id', 'name', 'phone', 'email', 'specialization', 'clinic_name']);
        $this->commission_percentage = 0;
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
    }

    public function render()
    {
        // Fetch only users who have a DoctorProfile attached to the current company
        $doctors = User::whereHas('doctorProfile', function($query) {
                $query->where('company_id', auth()->user()->company_id);
            })
            ->with('doctorProfile') 
            ->where(function($q) {
                $q->where('name', 'ilike', '%' . $this->searchTerm . '%')
                  ->orWhere('phone', 'ilike', '%' . $this->searchTerm . '%')
                  ->orWhereHas('doctorProfile', function($query2) {
                      $query2->where('clinic_name', 'ilike', '%' . $this->searchTerm . '%');
                  });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.lab.doctor-manager', [
            'doctors' => $doctors
        ])->layout('layouts.app', ['title' => 'Referring Doctors']);
    }
}