<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AppraisalCriterionController;
use App\Http\Controllers\AppraisalController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\SalesProofController;
use App\Http\Controllers\SalesValidationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==========================
// AUTHENTICATION (LOGIN/LOGOUT)
// ==========================
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/', [AuthenticatedSessionController::class, 'store']);
});

// ==========================
// SALES PROOF (ADMINISTRATOR)
// ==========================
Route::middleware(['auth', 'verified', 'role:administrator'])->group(function () {
    Route::resource('sales-proofs', SalesProofController::class)->except(['index', 'show']);
});

// ==========================
// SALES PROOF (SHARED VIEWING)
// ==========================
Route::middleware(['auth', 'verified', 'role:owner,admin,administrator,manager'])->group(function () {
    Route::get('sales-proofs', [SalesProofController::class, 'index'])->name('sales-proofs.index');
    Route::get('sales-proofs/{sales_proof}', [SalesProofController::class, 'show'])->name('sales-proofs.show');
});

// ==========================
// RUTE SETELAH LOGIN (SEMUA)
// ==========================
Route::middleware(['auth', 'verified'])->group(function () {
    // Ubah Password
    Route::get('/absensi/change-password', [AbsensiController::class, 'showChangePasswordForm'])->name('absensi.change-password');
    Route::post('/absensi/change-password', [AbsensiController::class, 'changePassword'])->name('absensi.change-password.store');

    // Logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// ==========================
// RUTE OWNER / ADMIN / MANAGER
// ==========================
Route::middleware(['auth', 'verified', 'role:owner,admin,manager'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Employee & Appraisal
    Route::resource('employees', EmployeeController::class);
    Route::resource('appraisal-criteria', AppraisalCriterionController::class);
    Route::resource('appraisals', AppraisalController::class);

    // Attendance Admin
    Route::get('/admin/attendances', [AdminAttendanceController::class, 'index'])->name('admin.attendances.index');
    Route::get('/admin/attendances/{employee}/{date}/edit', [AdminAttendanceController::class, 'editOrCreate'])->name('admin.attendances.edit_or_create');
    Route::put('/admin/attendances/{attendance}', [AdminAttendanceController::class, 'update'])->name('admin.attendances.update');
    Route::post('/admin/attendances/store-manual', [AdminAttendanceController::class, 'storeManual'])->name('admin.attendances.store_manual');
    Route::get('/admin/attendances/monthly-report', [AdminAttendanceController::class, 'monthlyReport'])->name('admin.attendances.monthly_report');

    // Settings - khusus admin
    Route::get('/admin/settings', [AdminSettingsController::class, 'index'])->middleware('role:admin')->name('admin.settings.index');
    Route::get('/admin/attendances/monthly-report/export-pdf', [AdminAttendanceController::class, 'exportMonthlyReportPdf'])
        ->name('admin.attendances.monthly_report.export_pdf');
    // Sales Validation (Manager)
    Route::middleware('role:manager')->group(function () {
        Route::get('sales-validations/{salesProof}/validate-form', [SalesValidationController::class, 'showValidationForm'])->name('sales-validations.validate-form');
        Route::resource('sales-validations', SalesValidationController::class)->only(['index', 'store']);
    });


    // Laporan Penjualan
    Route::get('/sales-reports', [SalesProofController::class, 'salesReportIndex'])->name('sales-reports.index');
});

// ==========================
// RUTE KARYAWAN / ADMINISTRATOR / LAINNYA
// ==========================
Route::middleware(['auth', 'verified', 'role:karyawan,administrator,owner,admin,manager'])->group(function () {
    Route::get('/absensi/dashboard', [AbsensiController::class, 'dashboard'])->name('absensi.dashboard');
    Route::post('/absensi/checkin', [AbsensiController::class, 'checkIn'])->name('absensi.checkin');
    Route::post('/absensi/checkout', [AbsensiController::class, 'checkOut'])->name('absensi.checkout');
});

// ==========================
// RUTE DARI BREEZE
// ==========================
require __DIR__.'/auth.php';
