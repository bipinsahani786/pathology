<?php

namespace App\Livewire\Lab;

use App\Models\DoctorProfile;
use App\Models\User;
use App\Services\Import\BulkImportService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination; // Required for Postgres-safe unique validation

class DoctorManager extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->authorize('view doctors');
    }

    // State variables
    public $searchTerm = '';

    public $user_id = null; // Tracks the User ID for editing

    // User Table Fields
    public $name;

    public $phone;

    public $email;

    public $password;

    // Doctor Profile Fields
    public $specialization;

    public $clinic_name;

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
     * Open modal to create a new doctor
     */
    public function create()
    {
        $this->authorize('create doctors');
        $this->resetFields();
        $this->isModalOpen = true;
    }

    /**
     * Load existing doctor data and open modal for editing
     */
    public function edit($id)
    {
        $this->authorize('edit doctors');
        $this->resetFields();

        // Eager load the profile to avoid N+1 query issues
        $user = User::where('company_id', auth()->user()->company_id)->with('doctorProfile')->findOrFail($id);

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
                'nullable',
                'numeric',
                'digits:10',
            ],
            'email' => [
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($this->user_id),
            ],
            'specialization' => 'nullable|string|max:255',
            'clinic_name' => 'nullable|string|max:255',
            'commission_percentage' => 'required|numeric|min:0|max:100',
            'password' => $this->user_id ? 'nullable|min:6' : 'nullable|min:6',
        ]);

        DB::beginTransaction();
        try {
            if ($this->user_id) {
                $this->authorize('edit doctors');
                // UPDATE EXISTING DOCTOR
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

                DoctorProfile::where('user_id', $this->user_id)->update([
                    'specialization' => $this->specialization,
                    'clinic_name' => $this->clinic_name,
                    'commission_percentage' => $this->commission_percentage,
                ]);

                session()->flash('message', 'Doctor details updated successfully.');
            } else {
                $this->authorize('create doctors');
                $company = auth()->user()->company;

                // SaaS Plan Enforcement for Doctors
                $maxDoctors = $company->plan->features['doctors'] ?? -1;
                if ($maxDoctors != -1) {
                    $currentDoctorsCount = \App\Models\DoctorProfile::where('company_id', $company->id)->count();
                    if ($currentDoctorsCount >= $maxDoctors) {
                        $this->addError('name', "Plan Limit Reached! Your plan allows only {$maxDoctors} referring doctor(s). Upgrade your plan to add more.");

                        return;
                    }
                }

                $companyId = $company->id;
                // CREATE NEW DOCTOR

                $activeBranchId = session('active_branch_id', 'all');
                $roles = auth()->user()->roles->pluck('name')->toArray();
                $isGlobalAdmin = (auth()->user()->hasAnyRole(['lab_admin', 'super_admin']) ||
                                 collect($roles)->contains(fn ($r) => str_ends_with($r, '_admin') || str_ends_with($r, '_super_admin') || str_contains(strtolower($r), 'admin')))
                                 && ! auth()->user()->hasRole('branch_admin');

                $myBranchId = $isGlobalAdmin
                    ? ($activeBranchId === 'all' ? null : $activeBranchId)
                    : auth()->user()->branch_id;

                // 1. Create the base User record (Prefixing Dr. if not provided can be done here)
                $finalName = str_starts_with(strtolower($this->name), 'dr') ? $this->name : 'Dr. '.$this->name;

                $user = User::create([
                    'company_id' => $companyId,
                    'branch_id' => $myBranchId,
                    'name' => $finalName,
                    'phone' => $this->phone,
                    'email' => $this->email ?: null,
                    'password' => Hash::make($this->password ?? $this->phone ?? 'password123'),
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

                // Assign Role
                $user->assignRole('doctor');

                session()->flash('message', 'New referring doctor added successfully.');
            }

            DB::commit();
            $this->closeModal();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error saving doctor: '.$e->getMessage());
        }
    }

    /**
     * Toggle doctor account status (Active/Inactive)
     */
    public function toggleStatus($id)
    {
        $this->authorize('edit doctors');
        $user = User::where('company_id', auth()->user()->company_id)->findOrFail($id);
        $user->is_active = ! $user->is_active;
        $user->save();

        session()->flash('message', 'Doctor status updated to '.($user->is_active ? 'Active' : 'Inactive'));
    }

    /**
     * Delete a doctor (Will cascade delete their profile)
     */
    public function delete($id)
    {
        $this->authorize('delete doctors');

        if (\App\Models\Invoice::where('referred_by_doctor_id', $id)->exists()) {
            session()->flash('error', 'Cannot delete doctor as they are referred on existing invoices.');
            return;
        }

        User::where('company_id', auth()->user()->company_id)->findOrFail($id)->delete();
        session()->flash('message', 'Doctor deleted successfully.');
    }

    /**
     * Reset form fields
     */
    public function resetFields()
    {
        $this->reset(['user_id', 'name', 'phone', 'email', 'specialization', 'clinic_name', 'password']);
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
        $this->authorize('create doctors');
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
     * Download sample Excel template for doctors
     */
    public function downloadSampleTemplate(BulkImportService $importService)
    {
        return $importService->downloadSampleTemplate('doctors');
    }

    /**
     * Process bulk Excel/CSV import for doctors
     */
    public function processBulkImport(BulkImportService $importService)
    {
        $this->authorize('create doctors');

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

        $this->importSummary = $importService->importDoctors($filePath, $companyId, $myBranchId);
        $this->importFile = null;

        if ($this->importSummary['success'] > 0) {
            session()->flash('message', "Successfully imported {$this->importSummary['success']} doctor(s).");
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

        $shareDoctors = \App\Models\Configuration::getFor('branch_share_doctors', '1') === '1';

        // Fetch only users who have a DoctorProfile attached to the current company
        $query = User::whereHas('doctorProfile', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        });

        if ($myBranchId && ! $shareDoctors) {
            $query->where('branch_id', $myBranchId);
        }

        $doctors = $query->with('doctorProfile')
            ->where(function ($q) {
                $q->where('name', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('phone', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhereHas('doctorProfile', function ($query2) {
                        $query2->where('clinic_name', 'ilike', '%'.$this->searchTerm.'%');
                    });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.lab.doctor-manager', [
            'doctors' => $doctors,
        ])->layout('layouts.app', ['title' => 'Referring Doctors']);
    }
}
