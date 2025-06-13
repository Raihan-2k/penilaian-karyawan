<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Impor Controller yang kita gunakan
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AppraisalCriterionController;
use App\Http\Controllers\AppraisalController;
use App\Http\Controllers\AbsensiController; // Untuk sistem absensi karyawan
use App\Http\Controllers\Auth\AuthenticatedSessionController; // Untuk kustomisasi halaman login utama

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// --- Rute Halaman Login Utama (Untuk Manager/Admin) ---
// Mengarahkan URL root "/" langsung ke halaman login admin/manager
Route::get('/', [AuthenticatedSessionController::class, 'create'])
            ->middleware('guest') // Hanya bisa diakses oleh user yang belum login
            ->name('login'); // Memberikan nama rute 'login'

// Rute Dashboard default dari Laravel Breeze (untuk Manager/Admin setelah login)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// --- Rute Aplikasi Inti (Hanya Bisa Diakses Oleh Manager/Admin yang Sudah Login) ---
// Semua rute di dalam grup ini akan memerlukan autentikasi user
Route::middleware('auth')->group(function () {
    // Rute Profil User (dari Laravel Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rute Resource untuk Karyawan (CRUD penuh)
    // URL: /employees, /employees/create, /employees/{id}, dll.
    Route::resource('employees', EmployeeController::class);

    // Rute khusus untuk membuat akun absensi dari halaman detail karyawan (oleh Admin/Manager)
    // URL: POST /employees/{employee}/create-attendance-account
    Route::post('employees/{employee}/create-attendance-account', [EmployeeController::class, 'createAttendanceAccount'])
        ->name('employees.create-attendance-account');

    // Rute Resource untuk Kriteria Penilaian (CRUD penuh)
    // URL: /appraisal-criteria, /appraisal-criteria/create, dll.
    Route::resource('appraisal-criteria', AppraisalCriterionController::class);

    // Rute Resource untuk Penilaian (CRUD penuh) - (Controller perlu diimplementasikan nanti)
    // URL: /appraisals, /appraisals/create, dll.
    Route::resource('appraisals', AppraisalController::class);
});

// --- Rute Sistem Absensi Karyawan (Khusus untuk Karyawan) ---

// Rute untuk Halaman Login Absensi Karyawan (tanpa perlu login admin/manager)
Route::middleware('guest')->group(function () {
    // URL: /absensi/login
    Route::get('/absensi/login', [AbsensiController::class, 'showLoginForm'])->name('absensi.login.show');
    // URL: POST /absensi/login (untuk memproses login)
    Route::post('/absensi/login', [AbsensiController::class, 'login'])->name('absensi.login');
});

// Rute untuk Fitur Absensi Karyawan yang Sudah Login
// Menggunakan guard 'web_employee_login' yang telah kita definisikan di config/auth.php
Route::middleware('auth:web_employee_login')->group(function () {
    // URL: /absensi/dashboard (Halaman dashboard absensi)
    Route::get('/absensi/dashboard', [AbsensiController::class, 'dashboard'])->name('absensi.dashboard');
    // URL: POST /absensi/checkin (untuk proses check-in)
    Route::post('/absensi/checkin', [AbsensiController::class, 'checkIn'])->name('absensi.checkin');
    // URL: POST /absensi/checkout (untuk proses check-out)
    Route::post('/absensi/checkout', [AbsensiController::class, 'checkOut'])->name('absensi.checkout');
    // URL: POST /absensi/logout (untuk proses logout absensi)
    Route::post('/absensi/logout', [AbsensiController::class, 'logout'])->name('absensi.logout');

    // Rute untuk ganti password pertama kali (jika diperlukan)
    // URL: /absensi/change-password
    Route::get('/absensi/change-password', [AbsensiController::class, 'showChangePasswordForm'])->name('absensi.change-password');
    // URL: POST /absensi/change-password (untuk memproses ganti password)
    Route::post('/absensi/change-password', [AbsensiController::class, 'changePassword'])->name('absensi.change-password.store');
});


// Mengimpor rute otentikasi Breeze yang tersisa (misal: forgot password, dll)
// Pastikan rute 'register' sudah Anda hapus atau komentari di 'routes/auth.php'
require __DIR__.'/auth.php';