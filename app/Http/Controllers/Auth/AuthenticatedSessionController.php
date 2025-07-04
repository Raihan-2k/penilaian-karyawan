<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Employee;
use App\Providers\RouteServiceProvider;
// use Illuminate\Support\Facades\Schema; // Uncomment jika pakai pengecekan kolom last_login_at

class AuthenticatedSessionController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Proses login user (Employee).
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Autentikasi
        $request->authenticate();
        $request->session()->regenerate();

        /** @var \App\Models\Employee $loggedInEmployee */
        $loggedInEmployee = Auth::user();

        // Update last_login_at jika kolom tersedia (opsional)
        // if (Schema::hasColumn('employees', 'last_login_at')) {
        //     $loggedInEmployee->update(['last_login_at' => now()]);
        // }

        // Jika harus ganti password, arahkan ke halaman ganti password
        if ($loggedInEmployee->must_change_password) {
            return redirect()->route('absensi.change-password');
        }

        // Redirect berdasarkan role
        return match ($loggedInEmployee->role) {
            'manager'  => redirect()->route('dashboard'),
            'karyawan' => redirect()->route('absensi.dashboard'),
            default    => redirect()->route('dashboard'),
        };
    }

    /**
     * Logout user dan akhiri sesi.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
