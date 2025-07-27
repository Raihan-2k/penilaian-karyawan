<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AbsensiController extends Controller
{
    protected $jamKerjaNormalPerHari = 8;

    public function __construct()
    {
        $this->middleware('role:owner,admin,manager,administrator,karyawan');
    }

    /**
     * Menampilkan dashboard absensi untuk karyawan yang login.
     */
    public function dashboard(): \Illuminate\View\View
    {
        /** @var \App\Models\User $loggedInUser */
        $loggedInUser = Auth::user();

        $employee = null;
        $isWorkingDayToday = false;
        $attendanceToday = null;
        $recentAttendances = collect();

        if ($loggedInUser->employee) {
            $employee = $loggedInUser->employee;
            $employee->load('shift');

            $today = Carbon::today();
            $isWorkingDayToday = $employee->shift ? $employee->shift->isWorkingDay($today) : false;

            $attendanceToday = Attendance::where('employee_id', $employee->id)
                                         ->whereDate('check_in_time', $today)
                                         ->first();

            $recentAttendances = Attendance::where('employee_id', $employee->id)
                                           ->whereDate('check_in_time', '>=', Carbon::now()->subDays(7))
                                           ->orderBy('check_in_time', 'desc')
                                           ->get();
        }

        if (!$employee && in_array($loggedInUser->role, ['karyawan', 'administrator'])) {
            abort(403, 'Profil karyawan Anda tidak ditemukan. Harap hubungi administrator.');
        }

        return view('absensi.dashboard', compact('loggedInUser', 'employee', 'attendanceToday', 'recentAttendances', 'isWorkingDayToday'));
    }

    /**
     * Proses check-in karyawan.
     */
    public function checkIn(Request $request): \Illuminate\Http\RedirectResponse
    {
        /** @var \App\Models\User $loggedInUser */
        $loggedInUser = Auth::user();
        $employee = $loggedInUser->employee;

        if (!$employee) {
            return back()->with('error', 'Anda tidak terdaftar sebagai karyawan untuk melakukan check-in.');
        }
        $employee->load('shift');
        $today = Carbon::today();

        // --- PERBAIKAN DI SINI ---
        // Cek apakah hari ini adalah hari libur berdasarkan shift
        if ($employee->shift && !$employee->shift->isWorkingDay($today)) {
            // Jika hari ini bukan hari kerja berdasarkan shift, dianggap hari libur
            return back()->with('error', 'Hari ini adalah hari libur Anda.'); // Mengubah pesan
        }

        $existingAttendance = Attendance::where('employee_id', $employee->id)
                                        ->whereDate('check_in_time', $today)
                                        ->first();

        if ($existingAttendance) {
            return back()->with('info', 'Anda sudah melakukan check-in hari ini.');
        }

        Attendance::create([
            'employee_id' => $employee->id,
            'date' => $today->toDateString(),
            'check_in_time' => Carbon::now(),
            'status' => 'present',
        ]);

        return back()->with('success', 'Check-in berhasil!');
    }

    /**
     * Proses check-out karyawan.
     */
    public function checkOut(Request $request): \Illuminate\Http\RedirectResponse
    {
        /** @var \App\Models\User $loggedInUser */
        $loggedInUser = Auth::user();
        $employee = $loggedInUser->employee;

        if (!$employee) {
            return back()->with('error', 'Anda tidak terdaftar sebagai karyawan untuk melakukan check-out.');
        }

        $today = Carbon::today();

        $attendance = Attendance::where('employee_id', $employee->id)
                                 ->whereDate('check_in_time', $today)
                                 ->whereNull('check_out_time')
                                 ->first();

        if (!$attendance) {
            return back()->with('error', 'Anda belum melakukan check-in hari ini atau sudah check-out.');
        }

        $checkOutTime = Carbon::now();

        $attendance->update([
            'check_out_time' => $checkOutTime,
            'overtime_hours' => 0,
        ]);

        return back()->with('success', 'Check-out berhasil!');
    }

    /**
     * Menampilkan form ganti password.
     */
    public function showChangePasswordForm(): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        /** @var \App\Models\User $loggedInUser */
        $loggedInUser = Auth::user();
        if (! $loggedInUser->must_change_password) {
            if (in_array($loggedInUser->role, ['admin', 'manager', 'owner'])) {
                return redirect()->route('dashboard');
            }
            return redirect()->route('absensi.dashboard');
        }
        return view('absensi.change-password', compact('loggedInUser'));
    }

    /**
     * Proses ganti password.
     */
    public function changePassword(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|same:password',
        ]);

        /** @var \App\Models\User $loggedInUser */
        $loggedInUser = Auth::user();

        $loggedInUser->password = Hash::make($request->password);
        $loggedInUser->must_change_password = false;
        $loggedInUser->save();

        if (in_array($loggedInUser->role, ['admin', 'manager', 'owner'])) {
            return redirect()->route('dashboard')->with('success', 'Password berhasil diubah!');
        }
        return redirect()->route('absensi.dashboard')->with('success', 'Password berhasil diubah!');
    }
}
