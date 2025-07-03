<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Impor Controller yang kita gunakan (PASTIKAN SEMUA MENGGUNAKAN BACKSLASH '\')
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AppraisalCriterionController;
use App\Http\Controllers\AppraisalController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
// use App\Http\Controllers\HolidayController; // <--- Pastikan ini dikomentari/dihapus jika fitur hari libur sudah dihapus
use App\Http\Controllers\AdminAttendanceController; // Untuk laporan absensi admin

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
    // Rute Profil User (sekarang Employee Manager)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rute Resource untuk Karyawan (CRUD penuh)
    Route::resource('employees', EmployeeController::class);

    // Rute Resource untuk Kriteria Penilaian
    Route::resource('appraisal-criteria', AppraisalCriterionController::class);

    // Rute Resource untuk Penilaian
    Route::resource('appraisals', AppraisalController::class);

    // Rute Resource untuk Hari Libur (jika sudah dihapus, pastikan tidak ada di sini)
    // Route::resource('holidays', HolidayController::class); // Contoh jika ada

    // --- Rute untuk Laporan Absensi Admin dan Edit/Buat Manual ---
    Route::get('/admin/attendances', [AdminAttendanceController::class, 'index'])->name('admin.attendances.index');
    // Rute Edit Terpadu (untuk membuat atau mengedit absensi)
    Route::get('/admin/attendances/{employee}/{date}/edit', [AdminAttendanceController::class, 'editOrCreate'])->name('admin.attendances.edit_or_create');
    // Rute Update untuk Record yang sudah ada
    Route::put('/admin/attendances/{attendance}', [AdminAttendanceController::class, 'update'])->name('admin.attendances.update');
    // Rute Store untuk Record Baru yang dibuat manual
    Route::post('/admin/attendances/store-manual', [AdminAttendanceController::class, 'storeManual'])->name('admin.attendances.store_manual');
    // --- AKHIR RUTE ABSENSI ADMIN ---

    // Rute Logout umum (untuk semua role)
    Route::post('/logout', [AbsensiController::class, 'logout'])->name('logout');
});

// --- Rute Sistem Absensi Karyawan (Hanya Bisa Diakses Oleh Karyawan Biasa yang Sudah Login) ---
Route::middleware(['auth'])->group(function () {
    // Dashboard Absensi Karyawan
    Route::get('/absensi/dashboard', [AbsensiController::class, 'dashboard'])->name('absensi.dashboard');
    // Proses Check-in
    Route::post('/absensi/checkin', [AbsensiController::class, 'checkIn'])->name('absensi.checkin');
    // Proses Check-out
    Route::post('/absensi/checkout', [AbsensiController::class, 'checkOut'])->name('absensi.checkout');
    // Rute untuk ganti password (untuk semua user, manager atau karyawan)
    Route::get('/absensi/change-password', [AbsensiController::class, 'showChangePasswordForm'])->name('absensi.change-password');
    Route::post('/absensi/change-password', [AbsensiController::class, 'changePassword'])->name('absensi.change-password.store');
});


// Mengimpor rute otentikasi Breeze yang tersisa (misal: forgot password, dll)
require __DIR__.'/auth.php';
