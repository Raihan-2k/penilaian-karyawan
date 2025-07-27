<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi input
        $request->validate([
            'nip' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // 1. Cari Employee berdasarkan NIP
        $employee = Employee::where('nip', $request->nip)->first();

        if (!$employee) {
            throw ValidationException::withMessages([
                'nip' => __('NIP tidak ditemukan.'),
            ]);
        }

        // 2. Dapatkan User yang berelasi dengan Employee tersebut
        /** @var \App\Models\User $user */
        $user = $employee->user;

        if (!$user) {
            throw ValidationException::withMessages([
                'nip' => __('Akun pengguna tidak ditemukan untuk NIP ini.'),
            ]);
        }

        // 3. Coba autentikasi menggunakan kredensial User
        if (!Auth::attempt(['id' => $user->id, 'password' => $request->password], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'password' => __('Password yang diberikan tidak cocok dengan NIP yang diberikan.'),
            ]);
        }

        // Jika autentikasi berhasil, regenerasi sesi dan redirect
        $request->session()->regenerate();

        // Periksa apakah user harus ganti password (jika Anda memiliki kolom must_change_password)
        if ($user->must_change_password) {
            return redirect()->intended(route('absensi.change-password', absolute: false));
        }

        // --- PERBAIKAN DI SINI ---
        // Redirect ke absensi.dashboard jika peran adalah 'karyawan' ATAU 'administrator'
        if (in_array($user->role, ['karyawan', 'administrator'])) {
    return redirect(route('absensi.dashboard', absolute: false));
}

        // Untuk role lain (owner, admin, manager) akan ke dashboard umum
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
