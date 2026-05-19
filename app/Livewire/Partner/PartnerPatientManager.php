<?php

namespace App\Livewire\Partner;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Invoice;
use App\Models\User;
use App\Models\PatientProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PartnerPatientManager extends Component
{
    use WithPagination;

    // Patient profile edit state variables
    public $isModalOpen = false;
    public $user_id = null;
    public $name;
    public $phone;
    public $email;
    public $age;
    public $age_type = 'Years';
    public $gender = 'Male';
    public $blood_group;
    public $address;

    public $search = '';
    public $perPage = 10;
    public $filterDateFrom;
    public $filterDateTo;
    public $filterStatus = '';
    public $role;
    public $stats = [];

    public function mount()
    {
        $user = Auth::user();
        $roles = $user->roles->pluck('name')->toArray();
        $isCC = $user->hasRole('collection_center') || $user->collection_center_id || collect($roles)->contains(fn($r) => str_contains(strtolower($r), 'collection'));
        $isDoctor = $user->hasRole('doctor') || $user->doctorProfile || collect($roles)->contains(fn($r) => str_contains(strtolower($r), 'doctor'));
        $isAgent = $user->hasRole('agent') || $user->agentProfile || collect($roles)->contains(fn($r) => str_contains(strtolower($r), 'agent'));

        if ($isDoctor) {
            $this->role = 'Doctor';
        } elseif ($isAgent) {
            $this->role = 'Agent';
        } elseif ($isCC) {
            $this->role = 'Collection Center';
        } else {
            abort(403, 'Unauthorized access: Role not recognized.');
        }

        $this->filterDateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->filterDateTo = now()->format('Y-m-d');
    }

    public function render()
    {
        $user = Auth::user();
        $query = Invoice::with(['patient.activeMembership.membership', 'items.labTest', 'testReport'])
            ->where('status', '!=', 'Cancelled');

        if ($this->role === 'Doctor') {
            $query->where('referred_by_doctor_id', $user->id);
        } elseif ($this->role === 'Agent') {
            $query->where('referred_by_agent_id', $user->id);
        } elseif ($this->role === 'Collection Center') {
            $query->where('collection_center_id', $user->collection_center_id);
        }

        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('invoice_number', 'like', $searchTerm)
                    ->orWhereHas('patient', function ($pq) use ($searchTerm) {
                        $pq->where('name', 'like', $searchTerm)
                            ->orWhere('phone', 'like', $searchTerm);
                    });
            });
        }

        if ($this->filterDateFrom) {
            $query->whereDate('invoice_date', '>=', $this->filterDateFrom);
        }
        if ($this->filterDateTo) {
            $query->whereDate('invoice_date', '<=', $this->filterDateTo);
        }
        if ($this->filterStatus) {
            $query->where('sample_status', $this->filterStatus);
        }

        // Stats calculation
        $statsQuery = Invoice::where('status', '!=', 'Cancelled');
        if ($this->role === 'Doctor') {
            $statsQuery->where('referred_by_doctor_id', $user->id);
        } elseif ($this->role === 'Agent') {
            $statsQuery->where('referred_by_agent_id', $user->id);
        } elseif ($this->role === 'Collection Center') {
            $statsQuery->where('collection_center_id', $user->collection_center_id);
        }

        $this->stats = [
            'total_patients' => (clone $statsQuery)->distinct('patient_id')->count(),
            'collected_today' => (clone $statsQuery)->whereDate('sample_collected_at', today())->count(),
            'awaiting_pickup' => (clone $statsQuery)->where('sample_status', 'Pending')->count(),
            'processing' => (clone $statsQuery)->where('sample_status', 'Processing')->count(),
        ];

        return view('livewire.partner.partner-patient-manager', [
            'invoices' => $query->latest()->paginate($this->perPage)
        ]);
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterDateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->filterDateTo = now()->format('Y-m-d');
        $this->filterStatus = '';
        $this->resetPage();
    }

    public function updateSampleStatus($invoiceId, $status)
    {
        $invoice = Invoice::findOrFail($invoiceId);

        $user = Auth::user();

        // Restriction for Collection Centers
        if ($this->role === 'Collection Center') {
            if ($invoice->collection_center_id != $user->collection_center_id) {
                session()->flash('error', 'Unauthorized access.');
                return;
            }

            $allowedStatuses = ['Pending', 'Collected', 'Dispatched'];
            if (!in_array($status, $allowedStatuses)) {
                session()->flash('error', 'Collection Centers can only update status up to Dispatched.');
                return;
            }
        }

        $invoice->update([
            'sample_status' => $status,
            'sample_collected_at' => ($status === 'Collected' && !$invoice->sample_collected_at) ? now() : $invoice->sample_collected_at
        ]);

        session()->flash('message', 'Sample status updated to ' . $status);
    }

    public function edit($id)
    {
        $this->resetFields();
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

    public function store()
    {
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
            'age_type' => 'required|in:Years,Months,Days',
            'gender' => 'required|in:Male,Female,Other',
            'blood_group' => 'nullable|string|max:5',
        ]);

        DB::beginTransaction();
        try {
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
            DB::commit();
            $this->closeModal();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error saving patient: ' . $e->getMessage());
        }
    }

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
}
