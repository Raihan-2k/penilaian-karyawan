<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\EmployeeLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon; // Pastikan ini diimpor untuk bekerja dengan tanggal dan waktu

class AbsensiController extends Controller
{
    // Atur jam kerja normal per hari di sini. Ini akan digunakan untuk menghitung lembur.
    protected $jamKerjaNormalPerHari = 8;

    /**
     * Menampilkan halaman formulir login absensi karyawan.
     * Halaman ini akan diakses oleh karyawan untuk masuk ke sistem absensi.
     */
    public function showLoginForm()
    {
        // Mengembalikan view 'absensi.login'
        return view('absensi.login');
    }

    /**
     * Memproses permintaan login absensi karyawan.
     * Mengotentikasi karyawan berdasarkan NIP dan password.
     */
    public function login(Request $request)
    {
        // Memvalidasi input dari formulir login
        $request->validate([
            'nip' => 'required|numeric', // NIP wajib diisi dan harus berupa angka
            'password' => 'required', // Password wajib diisi
        ]);

        // Mencoba otentikasi menggunakan guard 'web_employee_login'
        // 'nip' digunakan sebagai username dan 'password' sebagai password
        // $request->boolean('remember') untuk fitur "ingat saya"
        if (Auth::guard('web_employee_login')->attempt($request->only('nip', 'password'), $request->boolean('remember'))) {
            // Jika login berhasil, regenerasi ID sesi untuk mencegah session fixation attacks
            $request->session()->regenerate();

            // Dapatkan instance EmployeeLogin yang sedang login.
            // Gunakan PHPDoc untuk membantu IDE mengenali tipe objek.
            /** @var \App\Models\EmployeeLogin $employeeLogin */
            $employeeLogin = Auth::guard('web_employee_login')->user();

            // Perbarui kolom 'last_login_at' di tabel employee_logins
            $employeeLogin->update(['last_login_at' => Carbon::now()]);

            // Periksa apakah karyawan harus mengganti password mereka (biasanya pada login pertama kali)
            if ($employeeLogin->must_change_password) {
                // Redirect ke halaman ganti password
                return redirect()->route('absensi.change-password');
            }

            // Redirect ke dashboard absensi jika login berhasil dan tidak perlu ganti password
            return redirect()->route('absensi.dashboard');
        }

        // Jika login gagal, kembalikan ke halaman sebelumnya dengan pesan error
        return back()->withErrors([
            'nip' => 'NIP atau password salah.',
        ])->onlyInput('nip'); // Hanya menjaga input NIP agar tidak perlu diisi ulang
    }

    /**
     * Menampilkan dashboard absensi karyawan.
     * Menampilkan status absensi karyawan hari ini.
     */
    public function dashboard()
    {
        // Dapatkan instance EmployeeLogin yang sedang login
        /** @var \App\Models\EmployeeLogin $employeeLogin */
        $employeeLogin = Auth::guard('web_employee_login')->user();

        // Ambil data karyawan terkait dari relasi loginAccount
        $employee = $employeeLogin->employee;

        // Cari data absensi karyawan untuk hari ini
        $todayAttendance = Attendance::where('employee_id', $employee->id)
                                    ->where('date', Carbon::today()->toDateString()) // Mencari berdasarkan tanggal hari ini
                                    ->first(); // Ambil satu data pertama (jika ada)

        // Mengembalikan view 'absensi.dashboard' dengan data karyawan dan absensi hari ini
        return view('absensi.dashboard', compact('employee', 'todayAttendance'));
    }

    /**
     * Memproses permintaan check-in absensi.
     * Mencatat waktu masuk karyawan untuk hari ini.
     */
    public function checkIn(Request $request)
    {
        // Dapatkan instance EmployeeLogin yang sedang login
        /** @var \App\Models\EmployeeLogin $employeeLogin */
        $employeeLogin = Auth::guard('web_employee_login')->user();
        $employeeId = $employeeLogin->employee_id;
        $today = Carbon::today()->toDateString(); // Tanggal hari ini dalam format YYYY-MM-DD

        // Periksa apakah karyawan sudah melakukan check-in untuk hari ini
        $existingAttendance = Attendance::where('employee_id', $employeeId)
                                        ->where('date', $today)
                                        ->first();

        if ($existingAttendance) {
            // Jika sudah, kembalikan dengan pesan error
            return back()->with('error', 'Anda sudah melakukan check-in hari ini.');
        }

        // Buat record absensi baru di database
        Attendance::create([
            'employee_id' => $employeeId,
            'date' => $today,
            'check_in_time' => Carbon::now()->toTimeString(), // Catat waktu check-in saat ini
        ]);

        // Kembalikan dengan pesan sukses
        return back()->with('success', 'Check-in berhasil!');
    }

    /**
     * Memproses permintaan check-out absensi.
     * Mencatat waktu keluar karyawan dan menghitung jam lembur.
     */
    public function checkOut(Request $request)
    {
        // Dapatkan instance EmployeeLogin yang sedang login
        /** @var \App\Models\EmployeeLogin $employeeLogin */
        $employeeLogin = Auth::guard('web_employee_login')->user();
        $employeeId = $employeeLogin->employee_id;
        $today = Carbon::today()->toDateString();

        // Cari record absensi hari ini yang belum memiliki waktu check-out
        $attendance = Attendance::where('employee_id', $employeeId)
                                ->where('date', $today)
                                ->whereNull('check_out_time') // Mencari yang check_out_time-nya masih null
                                ->first();

        if (!$attendance) {
            // Jika tidak ada record absensi atau sudah check-out, kembalikan dengan pesan error
            return back()->with('error', 'Anda belum melakukan check-in hari ini atau sudah check-out.');
        }

        // Parse waktu check-in dari database menjadi objek Carbon
        $checkInTime = Carbon::parse($attendance->check_in_time);
        // Dapatkan waktu check-out saat ini
        $checkOutTime = Carbon::now();

        // Hitung total durasi kerja dalam menit, lalu konversi ke jam
        $totalWorkDuration = $checkOutTime->diffInMinutes($checkInTime);
        $totalWorkHours = $totalWorkDuration / 60;

        $overtimeHours = 0;
        // Jika total jam kerja melebihi jam kerja normal, hitung lembur
        if ($totalWorkHours > $this->jamKerjaNormalPerHari) {
            $overtimeHours = $totalWorkHours - $this->jamKerjaNormalPerHari;
            // Bulatkan jam lembur ke 2 angka desimal untuk presisi
            $overtimeHours = round($overtimeHours, 2);
        }

        // Perbarui record absensi dengan waktu check-out dan jam lembur
        $attendance->update([
            'check_out_time' => $checkOutTime->toTimeString(),
            'overtime_hours' => $overtimeHours,
        ]);

        // Kembalikan dengan pesan sukses, termasuk informasi jam lembur
        return back()->with('success', 'Check-out berhasil! Jam lembur: ' . $overtimeHours . ' jam.');
    }

    /**
     * Menampilkan formulir untuk ganti password pertama kali bagi karyawan.
     * Karyawan akan diarahkan ke sini jika 'must_change_password' adalah true.
     */
    public function showChangePasswordForm()
    {
        // Dapatkan instance EmployeeLogin yang sedang login
        /** @var \App\Models\EmployeeLogin $employeeLogin */
        $employeeLogin = Auth::guard('web_employee_login')->user();

        // Jika karyawan sudah tidak perlu ganti password, redirect ke dashboard
        if (!$employeeLogin->must_change_password) {
            return redirect()->route('absensi.dashboard');
        }
        // Mengembalikan view formulir ganti password
        return view('absensi.change-password');
    }

    /**
     * Memproses permintaan ganti password pertama kali.
     * Memperbarui password karyawan dan menonaktifkan flag 'must_change_password'.
     */
    public function changePassword(Request $request)
    {
        // Validasi input password baru
        $request->validate([
            'password' => 'required|string|min:8|confirmed', // Wajib, string, minimal 8 karakter, harus sama dengan 'password_confirmation'
        ]);

        // Dapatkan instance EmployeeLogin yang sedang login
        /** @var \App\Models\EmployeeLogin $employeeLogin */
        $employeeLogin = Auth::guard('web_employee_login')->user();

        // Enkripsi password baru dan simpan
        $employeeLogin->password = Hash::make($request->password);
        // Setel 'must_change_password' menjadi false karena password sudah diganti
        $employeeLogin->must_change_password = false;
        $employeeLogin->save(); // Simpan perubahan ke database

        // Redirect ke dashboard absensi dengan pesan sukses
        return redirect()->route('absensi.dashboard')->with('success', 'Password berhasil diubah!');
    }

    /**
     * Memproses permintaan logout dari sistem absensi.
     */
    public function logout(Request $request)
    {
        // Logout dari guard 'web_employee_login'
        Auth::guard('web_employee_login')->logout();

        // Menginvalidasi sesi saat ini
        $request->session()->invalidate();
        // Meregenerasi token CSRF untuk keamanan
        $request->session()->regenerateToken();

        // Redirect ke halaman login absensi
        return redirect()->route('absensi.login.show');
    }
}