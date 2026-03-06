<?php

use App\Livewire\Lab\Dashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::get('/dashboard', Dashboard::class)->name('lab.dashboard');    
require __DIR__.'/settings.php';
