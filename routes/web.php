<?php

use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Admin\GlobalTestManager;
use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    return view('welcome'); });
Route::get('/login', Login::class)->name('login');


// ==========================================
// PROTECTED ROUTES (Must be logged in)
// ==========================================
Route::middleware(['auth'])->group(function () {

    // ----------------------------------------------------
    // 1. SUPER ADMIN ROUTES (Company Owner)
    // ----------------------------------------------------
    Route::middleware(['role:super_admin'])->prefix('admin')->name('admin.')->group(function () {

        // URL: /admin/dashboard  |  Route Name: admin.dashboard
        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');

        // Future Routes  examples:
        Route::get('/global-tests', GlobalTestManager::class)->name('global-tests'); 
        // Route::get('/manage-labs', ManageLabs::class)->name('manage-labs');
        // Route::get('/subscriptions', ManageSubscriptions::class)->name('subscriptions');
    });


    // ----------------------------------------------------
    // 2. LAB OWNER / TENANT ROUTES
    // ----------------------------------------------------
    Route::middleware(['role:lab_admin'])->prefix('lab')->name('lab.')->group(function () {

        // URL: /lab/dashboard  |  Route Name: lab.dashboard
        Route::get('/dashboard', \App\Livewire\Lab\Dashboard::class)->name('dashboard');

        // Future Routes ke examples:
        // Route::get('/patients', PatientsList::class)->name('patients');
        // Route::get('/billing', BillingCreate::class)->name('billing');
        // Route::get('/reports', ReportsList::class)->name('reports');
    });


    // ----------------------------------------------------
    // 3. PATIENT PORTAL ROUTES (Future)
    // ----------------------------------------------------
    Route::middleware(['role:patient'])->prefix('portal')->name('portal.')->group(function () {
        // Route::get('/dashboard', PatientDashboard::class)->name('dashboard');
    });

});

// Settings
require __DIR__ . '/settings.php';
