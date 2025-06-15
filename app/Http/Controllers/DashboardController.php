<?php

namespace App\Http\Controllers;

use App\Models\Employee; // Untuk menghitung total karyawan
use App\Models\Appraisal; // Untuk menghitung penilaian
use App\Models\Attendance; // Untuk menghitung absensi dan lembur
use Carbon\Carbon; // Untuk bekerja dengan tanggal dan waktu
use Illuminate\Http\Request; // Untuk handle request HTTP (meskipun tidak digunakan langsung di index ini)

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama dengan data real-time.
     * Metode ini mengambil berbagai statistik dan aktivitas terbaru dari database.
     */
    public function index()
    {
        // 1. Data Statistik untuk Kartu (Cards)

        // Mengambil total jumlah karyawan dari tabel 'employees'
        $totalEmployees = Employee::count();

        // Menghitung jumlah penilaian yang telah selesai pada bulan dan tahun saat ini
        $appraisalsThisMonth = Appraisal::whereMonth('appraisal_date', Carbon::now()->month)
                                        ->whereYear('appraisal_date', Carbon::now()->year)
                                        ->count();

        // Menghitung total jam lembur yang tercatat pada bulan dan tahun saat ini
        // Fungsi sum() akan menjumlahkan nilai kolom 'overtime_hours'
        $overtimeThisMonth = Attendance::whereMonth('date', Carbon::now()->month)
                                        ->whereYear('date', Carbon::now()->year)
                                        ->sum('overtime_hours');
        // Membulatkan total jam lembur menjadi dua angka desimal
        $overtimeThisMonth = round($overtimeThisMonth, 2); 

        // Menghitung jumlah karyawan yang belum check-in pada hari ini
        // Pertama, ambil ID semua karyawan yang sudah check-in hari ini
        $checkedInEmployeeIdsToday = Attendance::where('date', Carbon::today()->toDateString())
                                                ->pluck('employee_id') // Ambil hanya kolom employee_id
                                                ->toArray(); // Konversi hasilnya menjadi array PHP
        // Kemudian, hitung total karyawan dikurangi yang sudah check-in hari ini
        $employeesNotCheckedInToday = $totalEmployees - count($checkedInEmployeeIdsToday);


        // 2. Data untuk Aktivitas Terbaru (Recent Activities)

        // Mengambil 5 penilaian terbaru, dengan eager loading data karyawan dan penilai
        // latest('appraisal_date') memastikan diurutkan berdasarkan tanggal penilaian terbaru
        $recentAppraisals = Appraisal::with(['employee', 'appraiser'])
                                    ->latest('appraisal_date') 
                                    ->limit(5) // Ambil hanya 5 data terbaru
                                    ->get();

        // Mengambil 5 data absensi terbaru, dengan eager loading data karyawan
        // latest('created_at') memastikan diurutkan berdasarkan waktu pembuatan record absensi terbaru
        $recentAttendances = Attendance::with('employee')
                                        ->latest('created_at') 
                                        ->limit(5) // Ambil hanya 5 data terbaru
                                        ->get();

        // Mengirim semua variabel yang telah diambil ke view 'dashboard'
        return view('dashboard', compact(
            'totalEmployees',             // Total karyawan
            'appraisalsThisMonth',        // Jumlah penilaian bulan ini
            'overtimeThisMonth',          // Total jam lembur bulan ini
            'employeesNotCheckedInToday', // Karyawan yang belum check-in hari ini
            'recentAppraisals',           // Penilaian terbaru
            'recentAttendances'           // Absensi terbaru
        ));
    }
}