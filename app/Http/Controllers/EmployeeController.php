<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeLogin;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; 
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * Menampilkan daftar semua karyawan.
     */
    public function index()
    {
        $employees = Employee::all(); // Mengambil semua data karyawan dari database
        return view('employees.index', compact('employees')); // Mengirim data ke view 'employees.index'
    }

    /**
     * Menampilkan form untuk membuat karyawan baru.
     */
    public function create()
    {
        return view('employees.create'); // Menampilkan view form tambah karyawan
    }

    /**
     * Menyimpan karyawan baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'nip' => 'required|digits:10|unique:employees,nip', // NIP wajib, harus angka, dan unik
            'name' => 'required|string|max:255', // Nama wajib, string, maksimal 255 karakter
            // 'email' => 'nullable|email|unique:employees,email', // Jika Anda ingin email ada di DB tapi opsional/tidak di form, bisa pakai ini
            'position' => 'required|string|max:255', // Posisi wajib, string, maksimal 255 karakter
            'hire_date' => 'required|date', // Tanggal masuk wajib, harus format tanggal
        ]);

        // Membuat record karyawan baru di database
        Employee::create($request->all());

        // Redirect kembali ke halaman daftar karyawan dengan pesan sukses
        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail karyawan tertentu.
     * Menggunakan Route Model Binding: Laravel otomatis menemukan karyawan berdasarkan ID di URL.
     */
    public function show(Employee $employee)
    {
        return view('employees.show', compact('employee')); // Menampilkan view detail karyawan
    }

    /**
     * Menampilkan form untuk mengedit karyawan.
     * Menggunakan Route Model Binding.
     */
    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee')); // Menampilkan view form edit karyawan
    }

    /**
     * Memperbarui data karyawan di database.
     * Menggunakan Route Model Binding.
     */
    public function update(Request $request, Employee $employee)
    {
        // Validasi input dari form, dengan pengecualian untuk NIP karyawan yang sedang diedit
        $request->validate([
            'nip' => ['required', 'digits:10', Rule::unique('employees', 'nip')->ignore($employee->id)],
            'name' => 'required|string|max:255',
            // 'email' => ['nullable', 'email', Rule::unique('employees', 'email')->ignore($employee->id)], // Jika email ada di DB
            'position' => 'required|string|max:255',
            'hire_date' => 'required|date',
        ]);

        // Memperbarui record karyawan di database
        $employee->update($request->all());

        // Redirect kembali ke halaman daftar karyawan dengan pesan sukses
        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil diperbarui!');
    }

    /**
     * Menghapus karyawan dari database.
     * Menggunakan Route Model Binding.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete(); // Menghapus record karyawan dari database
        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil dihapus!'); // Redirect dengan pesan sukses
    }
     public function createAttendanceAccount(Employee $employee)
    {
        // Cek apakah karyawan sudah punya akun absensi
        if ($employee->loginAccount) {
            return back()->with('error', 'Karyawan ini sudah memiliki akun absensi.');
        }

        // Generate password default (misal 123456)
        $defaultPassword = 'password123'; // Anda bisa membuat ini lebih dinamis atau random
        $hashedPassword = Hash::make($defaultPassword);

        EmployeeLogin::create([
            'employee_id' => $employee->id,
            'nip' => $employee->nip, // NIP dari data karyawan sebagai username
            'password' => $hashedPassword,
            'must_change_password' => true, // Memaksa ganti password saat login pertama
        ]);

        return back()->with('success', 'Akun absensi untuk NIP ' . $employee->nip . ' berhasil dibuat dengan password default: ' . $defaultPassword . '. Karyawan harus mengubah password saat login pertama.');
    }
}