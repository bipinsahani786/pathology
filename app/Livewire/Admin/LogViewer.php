<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;

class LogViewer extends Component
{
    public $logs = '';
    public $lines = 100;
    public $search = '';
    public $autoRefresh = false;

    public function mount()
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403);
        }
        $this->loadLogs();
    }

    public function loadLogs()
    {
        $logPath = storage_path('logs/laravel.log');

        if (!File::exists($logPath)) {
            $this->logs = "Log file not found at: " . $logPath;
            return;
        }

        // Use a simple tail command via PHP if possible, or read the file
        $file = new \SplFileObject($logPath, 'r');
        $file->seek(PHP_INT_MAX);
        $totalLines = $file->key();
        
        $startLine = max(0, $totalLines - $this->lines);
        $file->seek($startLine);

        $content = [];
        while (!$file->eof()) {
            $line = $file->fgets();
            if ($this->search && !str_contains(strtolower($line), strtolower($this->search))) {
                continue;
            }
            $content[] = $line;
        }

        $this->logs = implode("", array_reverse($content));
    }

    public function clearLog()
    {
        if (!auth()->user()->hasRole('super_admin')) return;
        
        $logPath = storage_path('logs/laravel.log');
        File::put($logPath, '');
        $this->loadLogs();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Log file cleared successfully.']);
    }

    public function render()
    {
        return view('livewire.admin.log-viewer')->layout('layouts.app');
    }
}
