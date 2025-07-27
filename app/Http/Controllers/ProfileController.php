<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest; // Ini adalah Request bawaan Breeze
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee; // Impor Model Employee
use App\Models\User;     // Impor Model User
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        /** @var \App\Models\User $user */
        // Menggunakan $request->user() adalah cara yang disarankan untuk mendapatkan user yang login
        $user = $request->user();

        // Dapatkan data karyawan yang berelasi jika ada
        // Ini akan null jika user bukan seorang karyawan (misal: owner, admin)
        $employee = $user->employee;

        // Kirim user dan employee ke view
        return view('profile.edit', compact('user', 'employee'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validatedUserData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->fill($validatedUserData);
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();

        if ($user->employee) {
            $employee = $user->employee;

            $validatedEmployeeData = $request->validate([
                'nip' => ['required', 'string', 'digits:10', Rule::unique('employees')->ignore($employee->id)],
                'position' => ['required', 'string', 'max:255'],
                'hire_date' => ['required', 'date'],
                'pendidikan_terakhir' => ['nullable', 'string', Rule::in(['SMA Sederajat', 'D3', 'S1', 'S2', 'S3'])],
                'nomor_telepon' => ['nullable', 'string', 'max:20'],
                'tanggal_lahir' => ['nullable', 'date'],
            ]);

            $employee->fill($validatedEmployeeData);
            $employee->save();
        }

        // Logika untuk mengubah password (jika ada di form)
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
