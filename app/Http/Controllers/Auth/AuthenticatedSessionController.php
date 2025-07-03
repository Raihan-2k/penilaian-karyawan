<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

use App\Models\Employee; // Untuk type hint
use Illuminate\Support\Facades\Route;
use App\Providers\RouteServiceProvider;

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
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate(); // Authenticates the user (now an Employee)

        $request->session()->regenerate();

        /** @var \App\Models\Employee $loggedInEmployee */
        $loggedInEmployee = Auth::user();

        // Perbarui last_login_at (jika Anda menambahkan kolom ini di migrasi Employee)
        // Anda perlu menambahkan kolom 'last_login_at' di migrasi 'employees' jika ingin menggunakannya
        // if (Schema::hasColumn('employees', 'last_login_at')) {
        //     $loggedInEmployee->update(['last_login_at' => now()]);
        // }

        // Cek apakah password perlu diganti (jika must_change_password true)
        if ($loggedInEmployee->must_change_password) {
            return redirect()->route('absensi.change-password');
        }

        // Redirect berdasarkan role
        if ($loggedInEmployee->role === 'manager') {
            return redirect()->intended(RouteServiceProvider::HOME); // Dashboard Manager
        } elseif ($loggedInEmployee->role === 'karyawan') {
            // Redirect karyawan ke dashboard absensi mereka
            return redirect()->intended(route('absensi.dashboard')); // <--- INI KRUSIAL
        }

        // Default redirect jika role tidak dikenali (fallback)
        return redirect()->intended(RouteServiceProvider::HOME);
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