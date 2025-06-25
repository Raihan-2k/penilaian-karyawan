<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee; // PENTING: Impor Model Employee
use Illuminate\Validation\Rule; // Penting: Impor Rule untuk validasi unique

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     * Sekarang user yang login adalah objek Employee.
     */
    public function edit(Request $request): View
    {
        /** @var \App\Models\Employee $user */ // Type hint untuk IDE
        $user = Auth::user();

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     * Sekarang user yang login adalah objek Employee.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var \App\Models\Employee $user */ // Type hint untuk IDE
        $user = Auth::user();

        // Validasi data profil yang di-update
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // Validasi email: bisa nullable (jika tidak wajib) dan unik ke tabel employees tapi ignore user ini sendiri
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', Rule::unique('employees')->ignore($user->id)], // Tambahkan nullable
            // Tambahkan validasi untuk NIP, jika ingin NIP juga bisa diubah dari profil
            'nip' => ['required', 'string', 'digits:10', Rule::unique('employees')->ignore($user->id)],
            // Kolom-kolom baru:
            'pendidikan_terakhir' => ['nullable', 'string', Rule::in(['SMA Sederajat', 'D3', 'S1', 'S2', 'S3'])],
            'nomor_telepon' => ['nullable', 'string', 'max:20'],
            'tanggal_lahir' => ['nullable', 'date'],
            // Kolom lain dari Employee
            'position' => ['required', 'string', 'max:255'],
            'hire_date' => ['required', 'date'],
            // Role tidak boleh diubah dari halaman profil, harus dari manajemen karyawan (EmployeeController)
        ]);

        // Jika ada perubahan pada email, reset email_verified_at (jika fitur verifikasi email digunakan)
        if ($user->isDirty('email')) { // Menggunakan $user->isDirty()
            $user->email_verified_at = null;
        }

        // Bagian untuk mengubah password dari form profil (opsional)
        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($request->password);
            $validatedData['must_change_password'] = false; // Jika ganti password dari profil, flag ini off
        }

        // Hapus password_confirmation karena tidak disimpan di DB
        unset($validatedData['password_confirmation']);

        // Update user
        $user->fill($validatedData);
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     * Sekarang user yang akan dihapus adalah objek Employee.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        /** @var \App\Models\Employee $user */ // Type hint untuk IDE
        $user = Auth::user(); // Dapatkan user yang sedang login

        Auth::logout(); // Logout user dari sesi

        $user->delete(); // Hapus record user (Employee) dari database

        $request->session()->invalidate(); // Invalidasi sesi
        $request->session()->regenerateToken(); // Regenerasi token CSRF

        return Redirect::to('/'); // Redirect ke halaman utama (login)
    }
}