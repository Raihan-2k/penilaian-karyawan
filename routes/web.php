<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AppraisalCriterionController;
use App\Http\Controllers\AppraisalController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\EmployeeTaskController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Rute Halaman Login Utama (Untuk Manager/Karyawan) ---
Route::get('/', [AuthenticatedSessionController::class, 'create'])
            ->middleware('guest')
            ->name('login');

// --- Rute Dashboard Utama (Untuk Manager) ---
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// --- Rute Aplikasi Manager (Hanya Bisa Diakses Oleh Manager yang Sudah Login) ---
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('employees', EmployeeController::class);
    Route::resource('appraisal-criteria', AppraisalCriterionController::class);
    Route::resource('appraisals', AppraisalController::class);
    Route::resource('tasks', TaskController::class);

    Route::get('/admin/attendances', [AdminAttendanceController::class, 'index'])->name('admin.attendances.index');
    Route::get('/admin/attendances/{employee}/{date}/edit', [AdminAttendanceController::class, 'editOrCreate'])->name('admin.attendances.edit_or_create');
    Route::put('/admin/attendances/{attendance}', [AdminAttendanceController::class, 'update'])->name('admin.attendances.update');
    Route::post('/admin/attendances/store-manual', [AdminAttendanceController::class, 'storeManual'])->name('admin.attendances.store_manual');

    Route::post('/logout', [AbsensiController::class, 'logout'])->name('logout');
});

// --- Rute Sistem Absensi Karyawan (Hanya Bisa Diakses Oleh Karyawan Biasa yang Sudah Login) ---
Route::middleware(['auth'])->group(function () {
    Route::get('/absensi/dashboard', [AbsensiController::class, 'dashboard'])->name('absensi.dashboard');
    Route::post('/absensi/checkin', [AbsensiController::class, 'checkIn'])->name('absensi.checkin');
    Route::post('/absensi/checkout', [AbsensiController::class, 'checkOut'])->name('absensi.checkout');
    Route::get('/absensi/change-password', [AbsensiController::class, 'showChangePasswordForm'])->name('absensi.change-password');
    Route::post('/absensi/change-password', [AbsensiController::class, 'changePassword'])->name('absensi.change-password.store');

    // --- Rute untuk Tugas Karyawan ---
    Route::get('/my-tasks', [EmployeeTaskController::class, 'index'])->name('employee-tasks.index');
    Route::get('/my-tasks/{task}', [EmployeeTaskController::class, 'show'])->name('employee-tasks.show');
    Route::get('/my-tasks/{task}/submit', [EmployeeTaskController::class, 'showSubmitForm'])->name('employee-tasks.submit_form');
    Route::post('/my-tasks/{task}/submit', [EmployeeTaskController::class, 'submit'])->name('employee-tasks.submit');
});

require __DIR__.'/auth.php';
