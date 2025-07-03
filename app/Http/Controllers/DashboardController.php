<?php

namespace App\Http\Controllers;

use App\Models\Employee; // Untuk menghitung karyawan
use App\Models\Appraisal; // Untuk penilaian
use App\Models\Attendance; // Untuk absensi
use Carbon\Carbon; // Untuk bekerja dengan tanggal dan waktu
// use Illuminate\Http\Request; // Baris ini tidak diperlukan jika Request tidak langsung digunakan di index()

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama dengan data real-time.
     */
    public function index()
    {
        // 1. Data Statistik (Cards)

        // Total Karyawan
        $totalEmployees = Employee::count();

        // Penilaian Selesai Bulan Ini
        $appraisalsThisMonth = Appraisal::whereMonth('appraisal_date', Carbon::now()->month)
                                        ->whereYear('appraisal_date', Carbon::now()->year)
                                        ->count();

        // Karyawan Belum Check-in Hari Ini
        // Dapatkan ID karyawan yang sudah check-in hari ini
        $checkedInEmployeeIdsToday = Attendance::where('date', Carbon::today()->toDateString())
                                                ->pluck('employee_id')
                                                ->toArray();
        // Hitung karyawan yang belum check-in
        $employeesNotCheckedInToday = $totalEmployees - count($checkedInEmployeeIdsToday);


        // 2. Aktivitas Terbaru

        // Penilaian Terbaru (5 data terakhir)
        $recentAppraisals = Appraisal::with(['employee', 'appraiser'])
                                    ->latest('appraisal_date')
                                    ->limit(5)
                                    ->get();

        // Absensi Terbaru (5 data terakhir, check-in atau check-out)
        $recentAttendances = Attendance::with('employee')
                                        ->latest('created_at')
                                        ->limit(5)
                                        ->get();

        // Kirim semua data ke view dashboard
        return view('dashboard', compact(
            'totalEmployees',
            'appraisalsThisMonth',
            'employeesNotCheckedInToday',
            'recentAppraisals',
            'recentAttendances'
        ));
    }
}