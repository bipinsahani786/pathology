<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class MaintenanceManager extends Component
{
    public function mount()
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403);
        }
    }

    public function runCommand($command)
    {
        if (!auth()->user()->hasRole('super_admin')) return;

        try {
            $commands = [
                'cache' => 'cache:clear',
                'view' => 'view:clear',
                'config' => 'config:clear',
                'route' => 'route:clear',
                'optimize' => 'optimize',
                'optimize_clear' => 'optimize:clear',
                'storage_link' => 'storage:link',
            ];

            if (!isset($commands[$command])) {
                session()->flash('error', 'Invalid command.');
                return;
            }

            Artisan::call($commands[$command]);
            $output = Artisan::output();
            
            Log::info("Superadmin ran maintenance command: " . $commands[$command], ['user_id' => auth()->id()]);
            
            session()->flash('message', "✅ Success: " . ($output ?: $commands[$command] . ' executed.'));
        } catch (\Exception $e) {
            Log::error("Maintenance Command Error: " . $e->getMessage());
            session()->flash('error', "❌ Error: " . $e->getMessage());
        }
    }

    public function clearAll()
    {
        if (!auth()->user()->hasRole('super_admin')) return;

        try {
            Artisan::call('optimize:clear');
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');
            
            Log::info("Superadmin ran FULL maintenance clear", ['user_id' => auth()->id()]);
            session()->flash('message', "✅ Full System Purge Complete! All caches, views, and configs cleared.");
        } catch (\Exception $e) {
            session()->flash('error', "❌ Full Clear Error: " . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.maintenance-manager')->layout('layouts.app', ['title' => 'System Maintenance']);
    }
}
