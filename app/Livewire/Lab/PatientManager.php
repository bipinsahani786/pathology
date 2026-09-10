<?php

namespace App\Livewire\Lab;

use App\Models\Configuration;
use App\Models\PatientProfile;
use App\Models\User;
use App\Services\Import\BulkImportService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination; // Required for Postgres-safe unique validation

class PatientManager extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        if (! auth()->user()->can('view patients') && ! auth()->user()->collection_center_id) {
            abort(403, 'Unauthorized.');
        }
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

    public $age_months;

    public $age_days;

    public $age_type = 'Years';

    public $gender = 'Male';

    public $blood_group;

    public $address;

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
     * Open modal to create a new patient
     */
    public function create()
    {
        if (! auth()->user()->can('create patients') && ! auth()->user()->collection_center_id) {
            abort(403, 'Unauthorized.');
        }
        $this->resetFields();
        $this->isModalOpen = true;
    }

    /**
     * Load existing patient data and open modal for editing
     */
    public function edit($id)
    {
        if (! auth()->user()->can('edit patients') && ! auth()->user()->collection_center_id) {
            abort(403, 'Unauthorized.');
        }
        $this->resetFields();

        // Eager load the profile to avoid N+1 query issues
        $user = User::where('company_id', auth()->user()->company_id)->with('patientProfile')->findOrFail($id);

        $this->user_id = $user->id;
        $this->name = $user->name;
        $this->phone = $user->phone;
        $this->email = $user->email;

        if ($user->patientProfile) {
            $type = $user->patientProfile->age_type ?? 'Years';
            $pAge = (int) ($user->patientProfile->age ?? 0);
            if ($type === 'Years') {
                $this->age = $pAge > 0 ? $pAge : '';
                $this->age_months = $user->patientProfile->age_months ?? '';
                $this->age_days = $user->patientProfile->age_days ?? '';
            } elseif ($type === 'Months') {
                $this->age = '';
                $this->age_months = $pAge > 0 ? $pAge : '';
                $this->age_days = $user->patientProfile->age_days ?? '';
            } elseif ($type === 'Days') {
                $this->age = '';
                $this->age_months = '';
                $this->age_days = $pAge > 0 ? $pAge : '';
            }
            $this->age_type = $type;
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
            ],
            'email' => [
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($this->user_id),
            ],
            'age' => 'nullable|integer|min:0|max:150',
            'age_months' => 'nullable|integer|min:0|max:11',
            'age_days' => 'nullable|integer|min:0|max:31',
            'gender' => 'required|in:Male,Female,Other',
            'blood_group' => 'nullable|string|max:5',
        ]);

        $years = (int) ($this->age ?: 0);
        $months = (int) ($this->age_months ?: 0);
        $days = (int) ($this->age_days ?: 0);

        if ($years === 0 && $months === 0 && $days === 0) {
            $this->addError('age', 'Please specify patient age (Years, Months, or Days).');
            return;
        }

        if ($years > 0) {
            $finalAge = $years;
            $finalAgeType = 'Years';
            $finalMonths = $months > 0 ? $months : null;
            $finalDays = $days > 0 ? $days : null;
        } elseif ($months > 0) {
            $finalAge = $months;
            $finalAgeType = 'Months';
            $finalMonths = null;
            $finalDays = $days > 0 ? $days : null;
        } else {
            $finalAge = $days;
            $finalAgeType = 'Days';
            $finalMonths = null;
            $finalDays = null;
        }

        DB::beginTransaction();
        try {
            if ($this->user_id) {
                if (! auth()->user()->can('edit patients') && ! auth()->user()->collection_center_id) {
                    abort(403, 'Unauthorized.');
                }
            } else {
                if (! auth()->user()->can('create patients') && ! auth()->user()->collection_center_id) {
                    abort(403, 'Unauthorized.');
                }
            }

            $companyId = auth()->user()->company_id;

            if ($this->user_id) {
                // UPDATE EXISTING PATIENT
                $user = User::where('company_id', auth()->user()->company_id)->findOrFail($this->user_id);
                $user->update([
                    'name' => $this->name,
                    'phone' => $this->phone,
                    'email' => $this->email,
                ]);

                PatientProfile::where('user_id', $this->user_id)->update([
                    'age' => $finalAge,
                    'age_type' => $finalAgeType,
                    'age_months' => $finalMonths,
                    'age_days' => $finalDays,
                    'gender' => $this->gender,
                    'blood_group' => $this->blood_group,
                    'address' => $this->address,
                ]);

                session()->flash('message', 'Patient details updated successfully.');
            } else {
                // CREATE NEW PATIENT

                // 1. Create the User record (Allows them to log in later)
                $activeBranchId = session('active_branch_id', 'all');
                $branchId = ($activeBranchId && $activeBranchId !== 'all') ? $activeBranchId : auth()->user()->branch_id;

                $user = User::create([
                    'name' => $this->name,
                    'phone' => $this->phone,
                    'email' => $this->email ?: null,
                    'password' => Hash::make($this->phone ?? '12345678'),
                    'is_active' => true,
                    'company_id' => $companyId,
                    'branch_id' => $branchId,
                ]);

                // 2. Generate a unique Patient ID from settings
                $pPrefix = Configuration::getFor('patient_id_prefix', 'PAT', $companyId, $branchId);
                $pDigits = (int) Configuration::getFor('patient_id_digits', 4, $companyId, $branchId);

                $maxLocalId = PatientProfile::where('company_id', $companyId)->max('company_patient_number') ?? 0;

                if ($maxLocalId == 0) {
                    // Fallback to parsing the latest patient_id_string for smooth transition
                    $lastProfile = PatientProfile::where('company_id', $companyId)
                                    ->whereNotNull('patient_id_string')
                                    ->orderBy('id', 'desc')
                                    ->first();
                    if ($lastProfile && strpos($lastProfile->patient_id_string, '-') !== false) {
                        $parsed = (int) preg_replace('/[^0-9]/', '', substr($lastProfile->patient_id_string, strrpos($lastProfile->patient_id_string, '-')));
                        if ($parsed > 0) {
                            $maxLocalId = $parsed;
                        }
                    }
                }

                $nextPId = $maxLocalId + 1;

                $patientIdString = $pPrefix . str_pad($nextPId, $pDigits, '0', STR_PAD_LEFT);

                // Loop until unique (safety valve)
                while (\App\Models\PatientProfile::where('company_id', $companyId)->where('patient_id_string', $patientIdString)->exists()) {
                    $nextPId++;
                    $patientIdString = $pPrefix . str_pad($nextPId, $pDigits, '0', STR_PAD_LEFT);
                }

                // 3. Create the Patient Profile record
                PatientProfile::create([
                    'company_id' => $companyId,
                    'user_id' => $user->id,
                    'patient_id_string' => $patientIdString,
                    'company_patient_number' => $nextPId,
                    'age' => $finalAge,
                    'age_type' => $finalAgeType,
                    'age_months' => $finalMonths,
                    'age_days' => $finalDays,
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
            session()->flash('error', 'Error saving patient: '.$e->getMessage());
        }
    }

    /**
     * Delete a patient (Will cascade delete their profile)
     */
    public function delete($id)
    {
        $this->authorize('delete patients');

        if (\App\Models\Invoice::where('patient_id', $id)->exists()) {
            session()->flash('error', 'Cannot delete patient as they have associated billing records.');
            return;
        }

        // Because of 'cascadeOnDelete' in migration, deleting the user deletes the profile too.
        User::where('company_id', auth()->user()->company_id)->findOrFail($id)->delete();
        session()->flash('message', 'Patient deleted successfully.');
    }

    /**
     * Reset form fields
     */
    public function resetFields()
    {
        $this->reset(['user_id', 'name', 'phone', 'email', 'age', 'age_months', 'age_days', 'blood_group', 'address']);
        $this->age_type = 'Years';
        $this->gender = 'Male';
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
        if (! auth()->user()->can('create patients') && ! auth()->user()->collection_center_id) {
            abort(403, 'Unauthorized.');
        }
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
     * Download sample Excel template
     */
    public function downloadSampleTemplate(BulkImportService $importService)
    {
        return $importService->downloadSampleTemplate('patients');
    }

    /**
     * Process bulk Excel/CSV import
     */
    public function processBulkImport(BulkImportService $importService)
    {
        if (! auth()->user()->can('create patients') && ! auth()->user()->collection_center_id) {
            abort(403, 'Unauthorized.');
        }

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
        $myBranchId = $user->hasRole('lab_admin') || $user->hasRole('super_admin')
            ? ($activeBranchId === 'all' ? null : $activeBranchId)
            : $user->branch_id;

        $this->importSummary = $importService->importPatients($filePath, $companyId, $myBranchId);
        $this->importFile = null;

        if ($this->importSummary['success'] > 0) {
            session()->flash('message', "Successfully imported {$this->importSummary['success']} patient(s).");
        }
        if ($this->importSummary['skipped'] > 0 && $this->importSummary['success'] === 0) {
            session()->flash('error', "Import completed with errors. Please check the summary above.");
        }
    }

    public function render()
    {
        $user = auth()->user();
        $companyId = $user->company_id;
        $restrictAccess = \App\Models\Configuration::getFor('restrict_branch_access', '1') === '1';
        $activeBranchId = session('active_branch_id', 'all');

        $roles = $user->roles->pluck('name')->toArray();
        $isGlobalAdmin = ($user->hasAnyRole(['lab_admin', 'super_admin']) ||
                         collect($roles)->contains(fn ($r) => str_ends_with($r, '_admin') || str_ends_with($r, '_super_admin') || str_contains(strtolower($r), 'admin')))
                         && ! $user->hasRole('branch_admin');

        $myBranchId = null;
        if ($isGlobalAdmin) {
            $myBranchId = ($activeBranchId === 'all' ? null : $activeBranchId);
        } else {
            $myBranchId = $user->branch_id;
        }

        // If strict branch access is enabled, force myBranchId if it was null AND user is NOT a global admin
        if ($restrictAccess && ! $myBranchId && ! $isGlobalAdmin) {
            $myBranchId = $user->branch_id;
        }

        $sharePatients = \App\Models\Configuration::getFor('branch_share_patients', '1') === '1';

        // Fetch only users who have a PatientProfile attached to the current company
        $query = User::whereHas('patientProfile', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        });

        // Strict isolation: Even if sharePatients is true, the browse list might be restricted
        // Usually: restrictAccess means you only see YOUR branch data.
        // sharePatients means you can find patients from other branches via Search (but not list them).

        if ($myBranchId && ! $sharePatients) {
            $query->where('branch_id', $myBranchId);
        } elseif ($myBranchId && $restrictAccess) {
            $query->where('branch_id', $myBranchId);
        }

        $patients = $query->with(['patientProfile', 'activeMembership.membership']) // Eager load to prevent slow queries
            ->where(function ($q) {
                $q->where('name', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('phone', 'like', '%'.$this->searchTerm.'%')
                    ->orWhereHas('patientProfile', function ($query2) {
                        $query2->where('patient_id_string', 'like', '%'.$this->searchTerm.'%');
                    });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.lab.patient-manager', [
            'patients' => $patients,
        ])->layout('layouts.app', ['title' => 'Patient Master']);
    }
}
