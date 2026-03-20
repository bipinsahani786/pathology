<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StaffRoleManager extends Component
{
    public $activeSubTab = 'staff'; // staff or roles

    // Staff State
    public $staff_id, $name, $email, $phone, $password, $role_id;
    public $isStaffModalOpen = false;

    // Role State
    public $role_id_to_edit, $role_name;
    public $selectedPermissions = [];
    public $isRoleModalOpen = false;

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount()
    {
        // Initial state
    }

    // ==========================================
    // STAFF MANAGEMENT
    // ==========================================
    
    public function createStaff()
    {
        $this->resetStaffFields();
        $this->isStaffModalOpen = true;
    }

    public function editStaff($id)
    {
        $this->resetStaffFields();
        $user = User::findOrFail($id);
        $this->staff_id = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->role_id = $user->roles->first()?->id;
        $this->isStaffModalOpen = true;
    }

    public function saveStaff()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required', 'email',
                Rule::unique('users')->ignore($this->staff_id),
            ],
            'phone' => [
                'required', 'numeric', 'digits:10',
                Rule::unique('users')->ignore($this->staff_id),
            ],
            'password' => $this->staff_id ? 'nullable|min:6' : 'required|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        DB::beginTransaction();
        try {
            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'company_id' => auth()->user()->company_id,
                'email_verified_at' => now(), // Auto verify staff emails
                'is_active' => true,
            ];

            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }

            if ($this->staff_id) {
                $user = User::findOrFail($this->staff_id);
                $user->update($data);
            } else {
                $user = User::create($data);
            }

            // Assign role by name to ensure guard consistency
            $role = Role::findById($this->role_id);
            $user->syncRoles([$role->name]);

            DB::commit();
            $this->isStaffModalOpen = false;
            $this->resetStaffFields();
            session()->flash('message', 'Staff member saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function deleteStaff($id)
    {
        if ($id == auth()->id()) {
            session()->flash('error', 'You cannot delete yourself.');
            return;
        }
        User::findOrFail($id)->delete();
        session()->flash('message', 'Staff member deleted.');
    }

    private function resetStaffFields()
    {
        $this->reset(['staff_id', 'name', 'email', 'phone', 'password', 'role_id']);
    }

    // ==========================================
    // ROLE MANAGEMENT
    // ==========================================

    public function createRole()
    {
        $this->resetRoleFields();
        $this->isRoleModalOpen = true;
    }

    public function editRole($id)
    {
        $this->resetRoleFields();
        $role = Role::findOrFail($id);
        
        // Don't allow editing system roles from here if needed
        // For now, allow everything
        
        $this->role_id_to_edit = $role->id;
        $this->role_name = str_replace('lab_' . auth()->user()->company_id . '_', '', $role->name);
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->isRoleModalOpen = true;
    }

    public function saveRole()
    {
        $this->validate([
            'role_name' => 'required|string|max:50',
            'selectedPermissions' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Lab specific role name to avoid global collisions
            $cleanName = strtolower(str_replace(' ', '_', $this->role_name));
            $internalName = 'lab_' . auth()->user()->company_id . '_' . $cleanName;

            if ($this->role_id_to_edit) {
                $role = Role::findOrFail($this->role_id_to_edit);
                $role->update(['name' => $internalName]);
            } else {
                $role = Role::create([
                    'name' => $internalName,
                    'guard_name' => 'web'
                ]);
            }

            $role->syncPermissions($this->selectedPermissions);

            DB::commit();
            $this->isRoleModalOpen = false;
            $this->resetRoleFields();
            session()->flash('message', 'Role saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    private function resetRoleFields()
    {
        $this->reset(['role_id_to_edit', 'role_name', 'selectedPermissions']);
    }

    public function render()
    {
        $labId = auth()->user()->company_id;

        // Get staff: Only show users with staff-related roles or NO role yet (to catch new staff)
        // EXCLUDE patients, doctors, and agents from this view
        $staff = User::where('company_id', $labId)
            ->where('id', '!=', auth()->id()) // Hide self from list
            ->whereDoesntHave('roles', function($query) {
                $query->whereIn('name', ['patient', 'doctor', 'agent']);
            })
            ->where(function($query) {
                // Either they have a role (and it's not excluded above)
                // OR they have no role but also NO profile (patient/doctor/agent)
                $query->has('roles')
                      ->orWhere(function($q) {
                          $q->doesntHave('patientProfile')
                            ->doesntHave('doctorProfile')
                            ->doesntHave('agentProfile');
                      });
            })
            ->with('roles')
            ->get();

        // Get roles: Lab specific roles + base staff role
        $roles = Role::where('name', 'like', 'lab_' . $labId . '_%')
            ->orWhereIn('name', ['staff', 'lab_admin']) 
            ->orderBy('id', 'asc')
            ->get();

        // Filter permissions: Exclude Super Admin only permissions
        $excludedPermissions = [
            'manage global_tests',
            'manage plans',
            'manage subscriptions',
            'manage departments' // System departments
        ];

        $permissions = Permission::whereNotIn('name', $excludedPermissions)
            ->where(function($query) {
                $query->where('name', 'like', 'manage %')
                      ->orWhere('name', 'like', '%reports');
            })
            ->get();

        return view('livewire.lab.staff-role-manager', [
            'staff' => $staff,
            'roles' => $roles,
            'permissions' => $permissions
        ]);
    }
}
