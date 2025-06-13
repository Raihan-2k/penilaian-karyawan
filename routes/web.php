<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AppraisalCriterionController;
use App\Http\Controllers\AppraisalController;

Route::get('/', function () {
    return view('welcome');
});

// Route Dashboard yang sudah ada dari Breeze
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Semua rute di dalam group ini hanya bisa diakses oleh user yang sudah login
Route::middleware('auth')->group(function () {
    // Rute Profil User (dari Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rute Resource untuk Karyawan (CRUD penuh)
    Route::resource('employees', EmployeeController::class);

    // Rute Resource untuk Kriteria Penilaian
    Route::resource('appraisal-criteria', AppraisalCriterionController::class);

    // Rute Resource untuk Penilaian
    Route::resource('appraisals', AppraisalController::class);
});

require __DIR__.'/auth.php';