<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash; // Penting: Impor ini untuk hash password

class EmployeeController extends Controller
{
    /**
     * Menampilkan daftar semua karyawan.
     */
    public function index()
    {
        $employees = Employee::all();
        return view('employees.index', compact('employees'));
    }

    /**
     * Menampilkan form untuk membuat karyawan baru.
     */
    public function create()
    {
        return view('employees.create');
    }

    /**
     * Menyimpan karyawan baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|digits:10|unique:employees,nip',
            'name' => 'required|string|max:255',
            'email' => 'email|max:255|unique:employees,email', // Validasi email
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', 'string', Rule::in(['manager', 'karyawan'])],
            'position' => 'required|string|max:255',
            'hire_date' => 'required|date',
            'pendidikan_terakhir' => 'string|max:255', // Validasi pendidikan terakhir
            'nomor_telepon' => 'string|max:20', // Validasi nomor telepon
            'tanggal_lahir' => 'date', // Validasi tanggal lahir
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->password);
        $data['must_change_password'] = true; // Karyawan baru passwordnya sudah diatur

        Employee::create($data);

        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail karyawan tertentu.
     */
    public function show(Employee $employee)
    {
        return view('employees.show', compact('employee'));
    }

    /**
     * Menampilkan form untuk mengedit karyawan.
     */
    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    /**
     * Memperbarui data karyawan di database.
     */
    public function update(Request $request, Employee $employee)
    {
        $rules = [
            'nip' => ['required', 'digits:10', Rule::unique('employees', 'nip')->ignore($employee->id)],
            'name' => 'required|string|max:255',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('employees', 'email')->ignore($employee->id)], // Validasi email
            'position' => 'required|string|max:255',
            'role' => ['required', 'string', Rule::in(['manager', 'karyawan'])],
            'hire_date' => 'required|date',
            'pendidikan_terakhir' => 'nullable|string|max:255', // Validasi pendidikan terakhir
            'nomor_telepon' => 'string|max:20', // Validasi nomor telepon
            'tanggal_lahir' => 'date', // Validasi tanggal lahir
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $request->validate($rules);

        $data = $request->all();

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
            $data['must_change_password'] = true; // Jika password diupdate, flag ini jadi false
        } else {
            // Hapus password dari data agar tidak mengupdate password jika tidak diisi
            unset($data['password']);
        }

        $employee->update($data);

        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil diperbarui!');
    }

    /**
     * Menghapus karyawan dari database.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil dihapus!');
    }
}