<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:owner,admin,manager');
    }

    public function index()
    {
        $employees = Employee::with(['user', 'shift'])->get();
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        /** @var \App\Models\User $loggedInUser */
        $loggedInUser = Auth::user();
        $allowedRolesForCreation = [];

        if ($loggedInUser->role === 'owner') {
            $allowedRolesForCreation = ['admin', 'manager'];
        } elseif ($loggedInUser->role === 'admin') {
            $allowedRolesForCreation = ['manager', 'administrator', 'karyawan'];
        } elseif ($loggedInUser->role === 'manager') {
            $allowedRolesForCreation = ['karyawan', 'admin', 'administrator'];
        } else {
            $allowedRolesForCreation = [];
        }

        $shifts = Shift::orderBy('name')->get();

        return view('employees.create', compact('allowedRolesForCreation', 'shifts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|digits:10|unique:employees,nip',
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', 'string', Rule::in(['owner', 'admin', 'manager', 'administrator', 'karyawan'])],
            'position' => 'required|string|max:255',
            'shift_id' => 'nullable|exists:shifts,id',
            'hire_date' => ['required', 'date'],
            'pendidikan_terakhir' => 'nullable|string|max:255',
            'nomor_telepon' => 'nullable|string|max:20',
            'tanggal_lahir' => 'nullable|date',
        ]);

        /** @var \App\Models\User $loggedInUser */
        $loggedInUser = Auth::user();

        $allowedRolesForLoggedInUser = [];
        if ($loggedInUser->role === 'owner') {
            $allowedRolesForLoggedInUser = ['admin', 'manager'];
        } elseif ($loggedInUser->role === 'admin') {
            $allowedRolesForLoggedInUser = ['manager', 'administrator', 'karyawan'];
        } elseif ($loggedInUser->role === 'manager') {
            $allowedRolesForLoggedInUser = ['karyawan', 'admin', 'administrator'];
        } elseif ($loggedInUser->role === 'administrator') {
            $allowedRolesForLoggedInUser = ['karyawan'];
        }

        if (!in_array($request->role, $allowedRolesForLoggedInUser)) {
            abort(403, 'Anda tidak memiliki izin untuk membuat akun dengan peran tersebut.');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'email_verified_at' => now(),
            'must_change_password' => true,
        ]);

        Employee::create([
            'user_id' => $user->id,
            'nip' => $request->nip,
            'name' => $request->name,
            'position' => $request->position,
            'shift_id' => $request->shift_id,
            'hire_date' => $request->hire_date,
            'pendidikan_terakhir' => $request->pendidikan_terakhir,
            'nomor_telepon' => $request->nomor_telepon,
            'tanggal_lahir' => $request->tanggal_lahir,
        ]);

        return redirect()->route('employees.index')->with('success', 'Karyawan baru berhasil ditambahkan dan akun pengguna telah dibuat!');
    }

    public function show(Employee $employee)
    {
        $employee->load(['user', 'shift']);
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        /** @var \App\Models\User $loggedInUser */
        $loggedInUser = Auth::user();

        if (
            ($employee->user->role === 'owner' && $loggedInUser->role !== 'owner') ||
            ($employee->user->role === 'admin' && ($loggedInUser->role === 'manager' || $loggedInUser->role === 'administrator')) ||
            ($employee->user->role === 'manager' && $loggedInUser->role === 'administrator')
        ) {
            abort(403, 'Anda tidak memiliki hak untuk mengedit akun dengan role tersebut.');
        }

        if ($loggedInUser->id === $employee->user->id) {
            // Biarkan dia mengedit data dirinya, tapi tidak mengubah role-nya di sini.
        }

        $allowedRolesForEdit = [];
        if ($loggedInUser->role === 'owner') {
            $allowedRolesForEdit = ['owner', 'admin', 'manager', 'administrator', 'karyawan'];
        } elseif ($loggedInUser->role === 'admin') {
            $allowedRolesForEdit = ['manager', 'administrator', 'karyawan'];
        } elseif ($loggedInUser->role === 'manager') {
            $allowedRolesForEdit = ['administrator', 'karyawan'];
        } else {
            // Untuk Administrator atau Karyawan, mereka tidak bisa mengedit role lain dari sini
            // (karena mereka tidak ada di middleware constructor)
            $allowedRolesForEdit = [$employee->user->role];
        }


        $employee->load(['user', 'shift']);
        $shifts = Shift::orderBy('name')->get();

        // --- PERBAIKAN DI SINI ---
        // Tambahkan $allowedRolesForEdit ke compact()
        return view('employees.edit', compact('employee', 'shifts', 'allowedRolesForEdit'));
    }

    public function update(Request $request, Employee $employee)
    {
        /** @var \App\Models\User $loggedInUser */
        $loggedInUser = Auth::user();

        if (
            ($employee->user->role === 'owner' && $loggedInUser->role !== 'owner') ||
            ($employee->user->role === 'admin' && ($loggedInUser->role === 'manager' || $loggedInUser->role === 'administrator')) ||
            ($employee->user->role === 'manager' && $loggedInUser->role === 'administrator')
        ) {
            abort(403, 'Anda tidak memiliki hak untuk memperbarui akun dengan role tersebut.');
        }

        $rules = [
            'nip' => ['required', 'digits:10', Rule::unique('employees', 'nip')->ignore($employee->id)],
            'name' => 'required|string|max:255',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($employee->user->id)],
            'position' => 'required|string|max:255',
            'shift_id' => 'nullable|exists:shifts,id',
            'role' => ['required', 'string', Rule::in(['owner', 'admin', 'manager', 'administrator', 'karyawan'])],
            'hire_date' => ['required', 'date'],
            'pendidikan_terakhir' => 'nullable|string|max:255',
            'nomor_telepon' => 'nullable|string|max:20',
            'tanggal_lahir' => 'nullable|date',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $request->validate($rules);

        $allowedRolesForLoggedInUserToEdit = [];
        if ($loggedInUser->role === 'owner') {
            $allowedRolesForLoggedInUserToEdit = ['owner', 'admin', 'manager', 'administrator', 'karyawan'];
        } elseif ($loggedInUser->role === 'admin') {
            $allowedRolesForLoggedInUserToEdit = ['manager', 'administrator', 'karyawan'];
        } elseif ($loggedInUser->role === 'manager') {
            $allowedRolesForLoggedInUserToEdit = ['administrator', 'karyawan'];
        } else {
            $allowedRolesForLoggedInUserToEdit = [$employee->user->role];
        }

        if (!in_array($request->role, $allowedRolesForLoggedInUserToEdit)) {
            abort(403, 'Anda tidak memiliki izin untuk mengubah peran karyawan ini ke peran tersebut.');
        }

        if ($loggedInUser->id === $employee->user->id) {
            if ($loggedInUser->role !== $request->role) {
                abort(403, 'Anda tidak dapat mengubah role akun Anda sendiri.');
            }
        }

        $user = $employee->user;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        $employee->nip = $request->nip;
        $employee->name = $request->name;
        $employee->position = $request->position;
        $employee->shift_id = $request->shift_id;
        $employee->hire_date = $request->hire_date;
        $employee->pendidikan_terakhir = $request->pendidikan_terakhir;
        $employee->nomor_telepon = $request->nomor_telepon;
        $employee->tanggal_lahir = $request->tanggal_lahir;
        $employee->save();

        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil diperbarui!');
    }

    public function destroy(Employee $employee)
    {
        /** @var \App\Models\User $loggedInUser */
        $loggedInUser = Auth::user();

        if (
            ($employee->user->role === 'owner' && $loggedInUser->role !== 'owner') ||
            ($employee->user->role === 'admin' && ($loggedInUser->role === 'manager' || $loggedInUser->role === 'administrator')) ||
            ($employee->user->role === 'manager' && $loggedInUser->role === 'administrator')
        ) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus akun dengan role tersebut.');
        }

        if ($employee->user->id === Auth::id()) {
            abort(403, 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user = $employee->user;

        if ($user) {
            $user->delete();
        } else {
            $employee->delete();
        }

        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil dihapus!');
    }
}
