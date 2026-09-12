<?php

namespace App\Livewire\Lab;

use App\Models\PhlebotomistProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class PhlebotomistManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $searchTerm = '';

    // Edit tracking
    public $user_id = null;

    // User fields
    public $name;
    public $phone;
    public $email;
    public $password;

    // Phlebotomist profile fields
    public $vehicle_number;
    public $vehicle_type;
    public $commission_per_visit = 0;
    public $working_hours_start = '08:00';
    public $working_hours_end  = '18:00';
    public $is_available = true;

    public $isModalOpen = false;

    public function mount()
    {
        // Plan-level feature gate
        $hasFeature = auth()->user()->company->plan?->features['home_collection'] ?? false;
        if (! $hasFeature) {
            abort(403, 'Home Collection module is not available on your current plan.');
        }
        $this->authorize('view phlebotomists');
    }

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->authorize('create phlebotomists');
        $this->resetFields();
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $this->authorize('edit phlebotomists');
        $this->resetFields();

        $user = User::where('company_id', auth()->user()->company_id)
            ->with('phlebotomistProfile')
            ->findOrFail($id);

        $this->user_id = $user->id;
        $this->name    = $user->name;
        $this->phone   = $user->phone;
        $this->email   = $user->email;

        if ($user->phlebotomistProfile) {
            $p = $user->phlebotomistProfile;
            $this->vehicle_number        = $p->vehicle_number;
            $this->vehicle_type          = $p->vehicle_type;
            $this->commission_per_visit  = $p->commission_per_visit;
            $this->working_hours_start   = $p->working_hours_start ?? '08:00';
            $this->working_hours_end     = $p->working_hours_end   ?? '18:00';
            $this->is_available          = $p->is_available;
        }

        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'name'                => 'required|string|max:255',
            'phone'               => ['nullable', 'numeric', 'digits:10'],
            'email'               => ['nullable', 'email', Rule::unique('users', 'email')->ignore($this->user_id)],
            'password'            => 'nullable|min:6',
            'vehicle_number'      => 'nullable|string|max:20',
            'vehicle_type'        => 'nullable|in:Bike,Car,Scooty,Other',
            'commission_per_visit'=> 'required|numeric|min:0',
            'working_hours_start' => 'nullable|date_format:H:i',
            'working_hours_end'   => 'nullable|date_format:H:i',
        ]);

        DB::beginTransaction();
        try {
            $companyId = auth()->user()->company_id;

            if ($this->user_id) {
                $this->authorize('edit phlebotomists');

                $user = User::where('company_id', $companyId)->findOrFail($this->user_id);
                $updateData = [
                    'name'  => $this->name,
                    'phone' => $this->phone,
                    'email' => $this->email,
                ];
                if ($this->password) {
                    $updateData['password'] = Hash::make($this->password);
                }
                $user->update($updateData);

                PhlebotomistProfile::where('user_id', $this->user_id)->update([
                    'vehicle_number'       => $this->vehicle_number,
                    'vehicle_type'         => $this->vehicle_type,
                    'commission_per_visit' => $this->commission_per_visit,
                    'working_hours_start'  => $this->working_hours_start,
                    'working_hours_end'    => $this->working_hours_end,
                    'is_available'         => $this->is_available,
                ]);

                session()->flash('message', 'Phlebotomist updated successfully.');
            } else {
                $this->authorize('create phlebotomists');

                $company = auth()->user()->company;

                // Plan limit enforcement
                $maxPhlebotomists = (int) ($company->plan->features['max_phlebotomists'] ?? 2);
                $currentCount = PhlebotomistProfile::where('company_id', $companyId)->count();
                if ($maxPhlebotomists !== -1 && $currentCount >= $maxPhlebotomists) {
                    $this->addError('name', "Your plan allows maximum {$maxPhlebotomists} phlebotomists. Please upgrade.");
                    DB::rollBack();
                    return;
                }

                $user = User::create([
                    'company_id' => $companyId,
                    'name'       => $this->name,
                    'phone'      => $this->phone,
                    'email'      => $this->email ?: null,
                    'password'   => Hash::make($this->password ?? $this->phone ?? 'password123'),
                    'is_active'  => true,
                ]);

                PhlebotomistProfile::create([
                    'company_id'           => $companyId,
                    'user_id'              => $user->id,
                    'vehicle_number'       => $this->vehicle_number,
                    'vehicle_type'         => $this->vehicle_type,
                    'commission_per_visit' => $this->commission_per_visit,
                    'working_hours_start'  => $this->working_hours_start,
                    'working_hours_end'    => $this->working_hours_end,
                    'is_available'         => true,
                ]);

                $user->assignRole('phlebotomist');
                session()->flash('message', 'Phlebotomist added successfully.');
            }

            DB::commit();
            $this->closeModal();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error saving phlebotomist: ' . $e->getMessage());
        }
    }

    public function toggleAvailability($id)
    {
        $this->authorize('edit phlebotomists');
        $profile = PhlebotomistProfile::where('user_id', $id)
            ->where('company_id', auth()->user()->company_id)
            ->firstOrFail();
        $profile->update(['is_available' => ! $profile->is_available]);
        session()->flash('message', 'Availability updated.');
    }

    public function delete($id)
    {
        $this->authorize('delete phlebotomists');

        $hasActiveVisits = \App\Models\HomeCollection::where('phlebotomist_id', $id)
            ->whereIn('status', ['Assigned', 'En Route', 'Arrived'])
            ->exists();

        if ($hasActiveVisits) {
            session()->flash('error', 'Cannot delete — phlebotomist has active visits assigned.');
            return;
        }

        User::where('company_id', auth()->user()->company_id)->findOrFail($id)->delete();
        session()->flash('message', 'Phlebotomist deleted successfully.');
    }

    public function resetFields()
    {
        $this->reset(['user_id', 'name', 'phone', 'email', 'password', 'vehicle_number', 'vehicle_type']);
        $this->commission_per_visit = 0;
        $this->working_hours_start  = '08:00';
        $this->working_hours_end    = '18:00';
        $this->is_available         = true;
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

        $phlebotomists = User::whereHas('phlebotomistProfile', fn ($q) => $q->where('company_id', $companyId))
            ->with('phlebotomistProfile')
            ->where(function ($q) {
                $q->where('name', 'ilike', '%' . $this->searchTerm . '%')
                  ->orWhere('phone', 'ilike', '%' . $this->searchTerm . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.lab.phlebotomist-manager', compact('phlebotomists'))
            ->layout('layouts.app', ['title' => 'Phlebotomists']);
    }
}
