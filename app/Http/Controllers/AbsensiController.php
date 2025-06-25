<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
// use App\Models\Holiday; // Pastikan ini dikomentari/dihapus jika fitur hari libur sudah dihapus
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class AbsensiController extends Controller
{
    protected $jamKerjaNormalPerHari = 8;

    /**
     * Menampilkan dashboard absensi karyawan.
     * Sekarang user yang login adalah objek Employee.
     */
    public function dashboard()
    {
        // Dapatkan user yang berhasil login (sekarang adalah objek Employee)
        /** @var \App\Models\Employee $loggedInEmployee */ // Type hint untuk IDE
        $loggedInEmployee = Auth::user();

        // PENTING: Periksa role. Hanya 'karyawan' yang boleh di sini.
        if ($loggedInEmployee->role !== 'karyawan') {
            // Jika bukan karyawan, redirect ke dashboard utama atau halaman error
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke dashboard absensi ini.');
        }

        // Ambil data karyawan terkait (sekarang sudah langsung dari $loggedInEmployee)
        $employee = $loggedInEmployee;

        // Ambil absensi hari ini (jika ada)
        $todayAttendance = Attendance::where('employee_id', $employee->id)
                                    ->where('date', Carbon::today()->toDateString())
                                    ->first();

        return view('absensi.dashboard', compact('employee', 'todayAttendance'));
    }

    /**
     * Proses Check-in.
     */
    public function checkIn(Request $request)
    {
        /** @var \App\Models\Employee $loggedInEmployee */ // Type hint untuk IDE
        $loggedInEmployee = Auth::user();
        $employeeId = $loggedInEmployee->id;
        $today = Carbon::today();

        // --- Logika Hari Libur (Sabtu/Minggu saja, tanpa Tanggal Merah) ---
        if ($today->isWeekend()) { // isWeekend() adalah metode Carbon untuk Sabtu/Minggu
            return back()->with('error', 'Hari ini adalah akhir pekan (Sabtu/Minggu), tidak perlu check-in.');
        }
        // --- Akhir Logika Hari Libur ---

        // Cek apakah sudah check-in hari ini
        $existingAttendance = Attendance::where('employee_id', $employeeId)
                                        ->where('date', $today->toDateString())
                                        ->first();

        if ($existingAttendance) {
            return back()->with('error', 'Anda sudah melakukan check-in hari ini.');
        }

        Attendance::create([
            'employee_id' => $employeeId,
            'date' => $today->toDateString(),
            'check_in_time' => Carbon::now()->toTimeString(),
        ]);

        return back()->with('success', 'Check-in berhasil!');
    }

    /**
     * Proses Check-out dan hitung lembur.
     */
    public function checkOut(Request $request)
    {
        /** @var \App\Models\Employee $loggedInEmployee */ // Type hint untuk IDE
        $loggedInEmployee = Auth::user();
        $employeeId = $loggedInEmployee->id;
        $today = Carbon::today()->toDateString();

        // Cari absensi hari ini yang belum check-out
        $attendance = Attendance::where('employee_id', $employeeId)
                                ->where('date', $today)
                                ->whereNull('check_out_time')
                                ->first();

        if (!$attendance) {
            return back()->with('error', 'Anda belum melakukan check-in hari ini atau sudah check-out.');
        }

        $checkInTime = $attendance->check_in_time; // Sudah Carbon karena di-cast di model
        $checkOutTime = Carbon::now();

        $overtimeHours = 0; // Inisialisasi

        // Perhitungan lembur (jika diaktifkan)
        // if ($checkInTime && $checkOutTime) {
        //     $start = $checkInTime;
        //     $end = $checkOutTime;
        //     $totalWorkDuration = abs($end->diffInMinutes($start));
        //     $overtimeHours = $this->calculateOvertimeHours($totalWorkDuration);
        // }

        $attendance->update([
            'check_out_time' => $checkOutTime->toTimeString(),
            'overtime_hours' => 0, // Set 0 karena fitur lembur sudah dihapus
        ]);

        // Pesan sukses tanpa informasi lembur
        return back()->with('success', 'Check-out berhasil!');
    }

    /**
     * Menampilkan form ganti password.
     * User yang login adalah objek Employee.
     */
    public function showChangePasswordForm()
    {
        /** @var \App\Models\Employee $loggedInEmployee */ // Type hint untuk IDE
        $loggedInEmployee = Auth::user();

        // Logika untuk memaksa ganti password (jika must_change_password true)
        if (! $loggedInEmployee->must_change_password) {
            // Jika sudah ganti password, redirect ke dashboard yang sesuai rolenya
            if ($loggedInEmployee->role === 'manager') {
                return redirect()->route('dashboard');
            }
            return redirect()->route('absensi.dashboard');
        }
        return view('absensi.change-password');
    }

    /**
     * Proses ganti password.
     * User yang login adalah objek Employee.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        /** @var \App\Models\Employee $loggedInEmployee */ // Type hint untuk IDE
        $loggedInEmployee = Auth::user();

        $loggedInEmployee->password = Hash::make($request->password);
        $loggedInEmployee->must_change_password = false;
        $loggedInEmployee->save();

        // Redirect ke dashboard yang sesuai rolenya setelah ganti password
        if ($loggedInEmployee->role === 'manager') {
            return redirect()->route('dashboard')->with('success', 'Password berhasil diubah!');
        }
        return redirect()->route('absensi.dashboard')->with('success', 'Password berhasil diubah!');
    }

    /**
     * Logout dari sesi.
     * Ini adalah metode logout umum yang akan digunakan untuk semua role.
     */
    public function logout(Request $request)
    {
        // Logout dari guard 'web' (yang sekarang menggunakan model Employee)
        Auth::guard('web')->logout();

        // Invalidasi sesi dan regenerasi token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect ke halaman login utama
        return redirect('/');
    }
    // Helper function calculateOvertimeHours dihapus karena fitur lembur dihapus.
}