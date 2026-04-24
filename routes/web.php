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
use App\Livewire\Lab\PosManager;
use App\Livewire\Lab\PosSummary;
use App\Livewire\Lab\SettingsManager;
use App\Livewire\Lab\InvoicePrint;
use App\Livewire\Lab\InvoiceManager;
use App\Livewire\Lab\PosEditManager;
use App\Livewire\Lab\SettlementManager;
use App\Livewire\Lab\ReportManager;
use App\Livewire\Lab\ResultEntryManager;
use App\Livewire\Lab\DepartmentManager;
use App\Livewire\Partner\PartnerDashboard;
use App\Livewire\Partner\PartnerProfile;
use Illuminate\Support\Facades\Route;


// Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Impersonation Routes
Route::get('/impersonate/start/{user}', [\App\Http\Controllers\ImpersonationController::class, 'loginAs'])->name('impersonate.start')->middleware('auth');
Route::get('/impersonate/stop', [\App\Http\Controllers\ImpersonationController::class, 'stopImpersonating'])->name('impersonate.stop')->middleware('auth');

Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/features', function () {
    return view('pages.features');
})->name('features');

Route::get('/pricing', function () {
    return view('pages.pricing');
})->name('pricing');

Route::get('/contact', \App\Livewire\Landing\ContactPage::class)->name('contact');

Route::post('/contact/submit', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'first_name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
    ]);
    
    \App\Models\Enquiry::create([
        'name' => trim($request->input('first_name') . ' ' . $request->input('last_name')),
        'email' => $request->input('email'),
        'lab_name' => $request->input('lab_name'),
        'message' => $request->input('message'),
        'status' => 'new',
        'enquiry_type' => 'website',
    ]);
    
    return redirect('/')->with('success', 'Thank you! We will get back to you shortly.');
})->name('contact.submit');

Route::get('/enquiry', \App\Livewire\Landing\EnquiryPage::class)->name('enquiry');

Route::get('/how-it-works', function () {
    return view('pages.how-it-works');
})->name('how-it-works');

Route::get('/faq', function () {
    return view('pages.faq');
})->name('faq');

Route::get('/terms', function () {
    return view('pages.terms');
})->name('terms');

Route::get('/privacy', function () {
    return view('pages.privacy');
})->name('privacy');

// Public Report & Bill Verification (for QR codes)
Route::get('/v/{hash}', [\App\Http\Controllers\PublicReportController::class, 'download'])->name('public.report.download');
Route::get('/bill/{hash}', [\App\Http\Controllers\InvoicePdfController::class, 'streamPublic'])->name('public.bill.download');


Route::middleware(['guest'])->group(function () {
    Route::get('/login', Login::class)->name('login');

// Route::get('/register-lab', RegisterCompany::class)->name('register.lab');
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

        // Global Master Test Library
        Route::get('/global-tests', GlobalTestManager::class)->name('global-tests');
        Route::get('/global-tests/create', \App\Livewire\Admin\GlobalTestEditor::class)->name('global-tests.create');
        Route::get('/global-tests/{id}/edit', \App\Livewire\Admin\GlobalTestEditor::class)->name('global-tests.edit');

        Route::get('/departments', \App\Livewire\Admin\DepartmentManager::class)->name('departments');
        Route::get('/plans', PlanManager::class)->name('plans');
        Route::get('/labs', \App\Livewire\Admin\LabManager::class)->name('labs');
        Route::get('/sales-agents', \App\Livewire\Admin\SalesAgentManager::class)->name('sales-agents');

        // CMS Management
        Route::get('/site-settings', \App\Livewire\Admin\SiteSettingsManager::class)->name('site-settings');
        Route::get('/landing-content', \App\Livewire\Admin\LandingContentManager::class)->name('landing-content');
        Route::get('/enquiries', \App\Livewire\Admin\EnquiryManager::class)->name('enquiries');
        Route::get('/audit-logs', \App\Livewire\Admin\AuditLogManager::class)->name('audit-logs');
    });


    // ----------------------------------------------------
    // 2. LAB OWNER / TENANT ROUTES
    // ----------------------------------------------------
    // Apply the 'auth', 'lab_staff', and our new subscription check middleware
    Route::middleware(['auth', 'lab_staff', \App\Http\Middleware\CheckTenantSubscription::class])
        ->prefix('lab')
        ->name('lab.')
        ->group(function () {

            // URL: /lab/dashboard  |  Route Name: lab.dashboard
            Route::get('/dashboard', Dashboard::class)->name('dashboard');


            // Billing & Upgrade Page (Users will be redirected here when their trial expires)
            Route::get('/upgrade-plan', function () {
                return view('lab.subscription-expired');
            })->name('billing.upgrade');

            Route::get('/subscription-expired', function () {
                return view('lab.subscription-expired');
            })->name('subscription.expired');



            // Lab Departments
            Route::get('/departments', DepartmentManager::class)->name('departments');

            // Lab Tests Management
            Route::get('/lab-tests', LabTestManager::class)->name('tests');
            Route::get('/lab-tests/create', \App\Livewire\Lab\LabTestEditor::class)->name('tests.create');
            Route::get('/lab-tests/{id}/edit', \App\Livewire\Lab\LabTestEditor::class)->name('tests.edit');

            // test packages and profiles
            Route::get('/test-packages', PackageManager::class)->name('packages');
            Route::get('/test-packages/create', \App\Livewire\Lab\PackageEditor::class)->name('packages.create');
            Route::get('/test-packages/{id}/edit', \App\Livewire\Lab\PackageEditor::class)->name('packages.edit');

            //membership and vouchers
            Route::get('/marketing', MarketingManager::class)->name('marketing');
            // Payment Modes
            Route::get('/payment-modes', PaymentModeManager::class)->name('payment.modes');
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

            // Settlements (Partner Commissions)
            Route::get('/settlements', SettlementManager::class)->name('settlements');

            // Point of Sale (Billing & Invoicing)
            Route::get('/pos', PosManager::class)->name('pos');
            Route::get('/pos/{invoice}/summary', PosSummary::class)->name('pos.summary');

            // Invoices Listing
            Route::get('/invoices', InvoiceManager::class)->name('invoices');

            // Settings
            Route::get('/settings', SettingsManager::class)->name('settings');
            Route::get('/audit-logs', \App\Livewire\Lab\AuditLogManager::class)->name('audit-logs')->middleware('can:view audit_logs');
            Route::get('/profile', PartnerProfile::class)->name('profile');
            Route::get('/invoice/{id}/pdf', [\App\Http\Controllers\InvoicePdfController::class, 'download'])->name('invoice.pdf');
            Route::get('/invoice/{id}/pdf-plain', [\App\Http\Controllers\InvoicePdfController::class, 'downloadWithoutHeader'])->name('invoice.pdf.plain');
            Route::get('/settings/invoice-preview/{template}', [\App\Http\Controllers\InvoicePdfController::class, 'previewTemplate'])->name('settings.invoice.preview');

            // Reports Generation
            Route::get('/reports', ReportManager::class)->name('reports');
            Route::get('/reports/entry/{id}', ResultEntryManager::class)->name('reports.entry');
            Route::get('/reports/print/{id}/{template?}', [\App\Http\Controllers\ReportPdfController::class, 'download'])->name('reports.print');

            // Invoice Print (browser)
            Route::get('/invoice/{id}/print', InvoicePrint::class)->name('invoice.print');

            // Invoice Edit (POS-style)
            Route::get('/invoice/{id}/edit', PosEditManager::class)->name('invoice.edit');

            // Barcode Stickers
            Route::get('/invoice/{id}/barcode-stickers', [\App\Http\Controllers\BarcodeController::class, 'printStickers'])->name('invoice.barcode.stickers');

            // Membership Card
            Route::get('/membership-card/{id}/print', [\App\Http\Controllers\MembershipCardController::class, 'print'])->name('membership.card.print');

            // Inventory Management
            Route::prefix('inventory')->name('inventory.')->middleware('can:view inventory')->group(function () {
                Route::get('/dashboard', \App\Livewire\Lab\Inventory\Dashboard::class)->name('dashboard');
                Route::get('/suppliers', \App\Livewire\Lab\Inventory\SupplierManager::class)->name('suppliers');
                Route::get('/items', \App\Livewire\Lab\Inventory\ItemManager::class)->name('items');
                Route::get('/stock', \App\Livewire\Lab\Inventory\StockManager::class)->name('stock');
                Route::get('/purchase', \App\Livewire\Lab\Inventory\PurchaseManager::class)->name('purchase');
                Route::get('/issuance', \App\Livewire\Lab\Inventory\IssuanceManager::class)->name('issuance');
            });
        });

    // ----------------------------------------------------
    // 3. PARTNER ROUTES (Doctor, Agent, Collection Center)
    // ----------------------------------------------------
    Route::middleware(['auth', 'partner_access', \App\Http\Middleware\CheckTenantSubscription::class])
        ->prefix('partner')
        ->name('partner.')
        ->group(function () {
            Route::get('/dashboard', PartnerDashboard::class)->name('dashboard');
            Route::get('/profile', PartnerProfile::class)->name('profile');
            Route::get('/patients', \App\Livewire\Partner\PartnerPatientManager::class)->name('patients');
            Route::get('/settlements', \App\Livewire\Partner\PartnerSettlementManager::class)->name('settlements');
            Route::get('/invoices', \App\Livewire\Partner\PartnerInvoiceManager::class)->name('invoices');
            Route::get('/referrers/doctors', \App\Livewire\Lab\DoctorManager::class)->name('doctors');
            Route::get('/referrers/agents', \App\Livewire\Lab\AgentManager::class)->name('agents');
            Route::get('/reports/print/{id}/{template?}', [\App\Http\Controllers\ReportPdfController::class, 'download'])->name('reports.print');
        });



});
 
// Global dashboard alias for tests/middleware
Route::get('/dashboard', function () {
    return redirect()->route('lab.dashboard');
})->middleware(['auth'])->name('dashboard');

// ----------------------------------------------------
// 4. PATIENT PORTAL ROUTES (External / Public Access)
// ----------------------------------------------------
Route::prefix('portal')->name('portal.')->group(function () {
    // Guest route for login (No Auth Middleware needed)
    Route::get('/login', \App\Livewire\Patient\PatientLogin::class)->name('login');

    // Protected routes (Only protected by our custom session middleware)
    Route::middleware(['auth', 'patient_portal_access'])->group(function () {
        Route::get('/dashboard', \App\Livewire\Patient\PatientDashboard::class)->name('dashboard');
        Route::get('/reports', \App\Livewire\Patient\PatientReports::class)->name('reports');
        Route::get('/membership', \App\Livewire\Patient\PatientMembership::class)->name('membership');
        Route::get('/profile', \App\Livewire\Patient\PatientProfile::class)->name('profile');
        
        // Dedicated Patient Report Download
        Route::get('/report/{id}/print', [\App\Http\Controllers\ReportPdfController::class, 'download'])->name('report.download');
        
        // Dedicated Patient Invoice Download
        Route::get('/invoice/{id}/print', [\App\Http\Controllers\InvoicePdfController::class, 'download'])->name('invoice.download');
    });
});

