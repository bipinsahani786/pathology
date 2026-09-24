<?php

use App\Http\Controllers\Api\V1\BookingApiController;
use App\Http\Controllers\Api\V1\BranchApiController;
use App\Http\Controllers\Api\V1\DepartmentApiController;
use App\Http\Controllers\Api\V1\PackageApiController;
use App\Http\Controllers\Api\V1\PatientAuthApiController;
use App\Http\Controllers\Api\V1\ReportTrackApiController;
use App\Http\Controllers\Api\V1\TestApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| External Lab Website REST APIs (Version 1)
| Protected by: lab_api_key (Multi-tenant company + Superadmin plan check)
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->middleware(['lab_api_key'])->group(function () {
    // 1. Branches & Collection Centers
    Route::get('/branches', [BranchApiController::class, 'index']);

    // 2. Departments / Test Categories
    Route::get('/departments', [DepartmentApiController::class, 'index']);

    // 3. Tests Catalog
    Route::get('/tests', [TestApiController::class, 'index']);

    // 4. Packages Catalog & Detail
    Route::get('/packages', [PackageApiController::class, 'index']);
    Route::get('/packages/{id}', [PackageApiController::class, 'show']);

    // 5. Online Booking / Appointments
    Route::post('/bookings', [BookingApiController::class, 'store']);

    // 6. Patient Report Tracking & PDF Download
    Route::post('/reports/track', [ReportTrackApiController::class, 'track']);

    // 7. Patient Portal Login (External Website SSO)
    Route::post('/patient/login', [PatientAuthApiController::class, 'login']);
});
