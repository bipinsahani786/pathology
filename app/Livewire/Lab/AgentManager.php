<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\AgentProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AgentManager extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->authorize('view agents');
    }

    // State variables
    public $searchTerm = '';
    public $user_id = null; // Tracks the User ID for editing
    
    // User Table Fields
    public $name;
    public $phone;
    public $email;
    public $password;
    
    // Agent Profile Fields
    public $agency_name;
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
     * Open modal to create a new agent
     */
    public function create()
    {
        $this->authorize('create agents');
        $this->resetFields();
        $this->isModalOpen = true;
    }

    /**
     * Load existing agent data and open modal for editing
     */
    public function edit($id)
    {
        $this->authorize('edit agents');
        $this->resetFields();
        
        // Eager load the profile to avoid N+1 query issues
        $user = User::with('agentProfile')->findOrFail($id);
        
        $this->user_id = $user->id;
        $this->name = $user->name;
        $this->phone = $user->phone;
        $this->email = $user->email;
        
        if ($user->agentProfile) {
            $this->agency_name = $user->agentProfile->agency_name;
            $this->commission_percentage = $user->agentProfile->commission_percentage;
        }

        $this->isModalOpen = true;
    }

    /**
     * Validate and save the agent data to both tables
     */
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
            'agency_name' => 'nullable|string|max:255',
            'commission_percentage' => 'required|numeric|min:0|max:100',
            'password' => $this->user_id ? 'nullable|min:6' : 'nullable|min:6',
        ]);

        DB::beginTransaction();
        try {
            if ($this->user_id) {
                $this->authorize('edit agents');
                // UPDATE EXISTING AGENT
                $user = User::findOrFail($this->user_id);
                $updateData = [
                    'name' => $this->name,
                    'phone' => $this->phone,
                    'email' => $this->email,
                ];

                if ($this->password) {
                    $updateData['password'] = Hash::make($this->password);
                }

                $user->update($updateData);

                AgentProfile::where('user_id', $this->user_id)->update([
                    'agency_name' => $this->agency_name,
                    'commission_percentage' => $this->commission_percentage,
                ]);

                session()->flash('message', 'Agent details updated successfully.');
            } else {
                $this->authorize('create agents');
                $companyId = auth()->user()->company_id;
                // CREATE NEW AGENT
                
                // 1. Create the base User record
                $user = User::create([
                    'name' => $this->name,
                    'phone' => $this->phone,
                    'email' => $this->email ?: null, 
                    'password' => Hash::make($this->password ?? $this->phone ?? 'password123'), 
                    'is_active' => true,
                ]);

                // 2. Create the Agent Profile record
                AgentProfile::create([
                    'company_id' => $companyId,
                    'user_id' => $user->id,
                    'agency_name' => $this->agency_name,
                    'commission_percentage' => $this->commission_percentage,
                ]);

                // Assign Role
                $user->assignRole('agent');

                session()->flash('message', 'New referral agent added successfully.');
            }

            DB::commit();
            $this->closeModal();
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error saving agent: ' . $e->getMessage());
        }
    }

    /**
     * Toggle agent account status (Active/Inactive)
     */
    public function toggleStatus($id)
    {
        $this->authorize('edit agents');
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();
        
        session()->flash('message', 'Agent status updated to ' . ($user->is_active ? 'Active' : 'Inactive'));
    }

    public function delete($id)
    {
        $this->authorize('delete agents');
        User::findOrFail($id)->delete();
        session()->flash('message', 'Agent deleted successfully.');
    }

    /**
     * Reset form fields
     */
    public function resetFields()
    {
        $this->reset(['user_id', 'name', 'phone', 'email', 'agency_name', 'password']);
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
        $companyId = auth()->user()->company_id;

        // Fetch only users who have an AgentProfile attached to the current company
        $agents = User::whereHas('agentProfile', function($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->with('agentProfile') 
            ->where(function($q) {
                $q->where('name', 'ilike', '%' . $this->searchTerm . '%')
                  ->orWhere('phone', 'ilike', '%' . $this->searchTerm . '%')
                  ->orWhereHas('agentProfile', function($query2) {
                      $query2->where('agency_name', 'ilike', '%' . $this->searchTerm . '%');
                  });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.lab.agent-manager', [
            'agents' => $agents
        ])->layout('layouts.app', ['title' => 'Referral Agents']);
    }
}