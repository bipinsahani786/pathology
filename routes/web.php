<?php

use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Admin\GlobalTestManager;
use App\Livewire\Admin\PlanManager;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\RegisterCompany;
use App\Livewire\Lab\AgentManager;
use App\Livewire\Lab\BranchManager;
use App\Livewire\Lab\CollectionCenterManager;
use App\Livewire\Lab\Dashboard;
use App\Livewire\Lab\DoctorManager;
use App\Livewire\Lab\LabTestManager;
use App\Livewire\Lab\MarketingManager;
use App\Livewire\Lab\PackageManager;
use App\Livewire\Lab\PatientManager;
use App\Livewire\Lab\PaymentModeManager;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    return view('welcome'); });

Route::middleware(['guest'])->group(function () {
    Route::get('/login', Login::class)->name('login');
    
    Route::get('/register-lab', RegisterCompany::class)->name('register.lab'); 
});


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
        Route::get('/plans', PlanManager::class)->name('plans');
        // Route::get('/manage-labs', ManageLabs::class)->name('manage-labs');
        // Route::get('/subscriptions', ManageSubscriptions::class)->name('subscriptions');
    });


    // ----------------------------------------------------
    // 2. LAB OWNER / TENANT ROUTES
    // ----------------------------------------------------
   // Apply the 'auth', 'role:lab_admin', and our new subscription check middleware
    Route::middleware(['role:lab_admin', \App\Http\Middleware\CheckTenantSubscription::class])
         ->prefix('lab')
         ->name('lab.')
         ->group(function () {

        // URL: /lab/dashboard  |  Route Name: lab.dashboard
        Route::get('/dashboard', Dashboard::class)->name('dashboard');

        // Billing & Upgrade Page (Users will be redirected here when their trial expires)
        Route::get('/upgrade-plan', function() {
            return "Your trial has expired. Please upgrade your plan to continue."; // Placeholder text for now
        })->name('billing.upgrade'); 



        // Lab Tests Management
        Route::get('/lab-tests', LabTestManager::class)->name('tests');
         // test packages and profiles
        Route::get('/test-packages',PackageManager::class)->name('packages');
        //membership and vouchers
        Route::get('/marketing', MarketingManager::class)->name('marketing');
        // Payment Modes
        Route::get('/payment-modes',PaymentModeManager::class)->name('payment.modes');
        //collection centers
        Route::get('/collection-centers', CollectionCenterManager::class)->name('collection.centers');
        //Branches
        Route::get('/branches', BranchManager::class)->name('branches');

        //patients
        Route::get('/patients', PatientManager::class)->name('patients');

        // Doctors
        Route::get('/doctors', DoctorManager::class)->name('doctors');

        //Agent
        Route::get('/agents', AgentManager::class)->name('agents');
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
