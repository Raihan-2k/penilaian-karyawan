<?php

namespace App\Http\Controllers;

use App\Models\SalesProof;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class SalesProofController extends Controller
{
    /**
     * Constructor dihapus karena middleware sekarang diterapkan di routes/web.php
     */
    // public function __construct()
    // {
    //     $this->middleware('role:administrator,owner,admin');
    // }

    /**
     * Menampilkan daftar bukti penjualan.
     * Dapat diakses oleh owner, admin, administrator.
     */
    public function index()
    {
        /** @var \App\Models\User $loggedInUser */
        $loggedInUser = Auth::user();

        $loggedInEmployeeId = $loggedInUser->employee ? $loggedInUser->employee->id : null;

        if (!$loggedInEmployeeId) {
            return redirect()->route('dashboard')->with('error', 'Profil karyawan Anda tidak ditemukan. Anda tidak dapat mengelola bukti penjualan.');
        }

        $salesProofs = SalesProof::with(['uploadedBy.user', 'validations.validatedBy.user'])
                                 ->latest()
                                 ->get();

        return view('sales-proofs.index', compact('salesProofs'));
    }

    /**
     * Menampilkan form untuk mengunggah bukti penjualan baru.
     * Hanya dapat diakses oleh administrator (sesuai route middleware).
     */
    public function create()
    {
        /** @var \App\Models\User $loggedInUser */
        $loggedInUser = Auth::user();

        if (!$loggedInUser->employee) {
            return redirect()->route('sales-proofs.index')->with('error', 'Profil karyawan Anda tidak ditemukan. Anda tidak dapat mengunggah bukti penjualan.');
        }

        // --- PERBAIKAN DI SINI ---
        // Administrator dapat mengunggah untuk SEMUA role karyawan.
        // Hapus filter ini.
        $employees = Employee::with('user')->orderBy('name')->get(); // Mengambil semua karyawan, tanpa filter role

        return view('sales-proofs.create', compact('employees'));
    }

    /**
     * Menyimpan bukti penjualan baru ke database.
     * Hanya dapat diakses oleh administrator (sesuai route middleware).
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'proof_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        /** @var \App\Models\User $loggedInUser */
        $loggedInUser = Auth::user();

        $loggedInEmployeeId = $loggedInUser->employee ? $loggedInUser->employee->id : null;

        if (!$loggedInEmployeeId) {
            return back()->with('error', 'Profil administrator Anda tidak ditemukan. Anda tidak dapat mengunggah bukti penjualan.');
        }

        // --- PERBAIKAN DI SINI ---
        // Tidak ada lagi validasi sisi server untuk role karyawan yang dipilih.
        // Administrator dapat mengunggah untuk SEMUA role karyawan.
        // Hapus blok ini.
        // $selectedEmployee = Employee::with('user')->find($request->employee_id);
        // if ($loggedInUser->role === 'administrator') {
        //     if ($selectedEmployee->user->role !== 'karyawan') {
        //         abort(403, 'Administrator hanya dapat mengunggah bukti penjualan untuk karyawan dengan peran Karyawan.');
        //     }
        // }


        $filePath = null;
        if ($request->hasFile('proof_file')) {
            $file = $request->file('proof_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('sales_proofs', $fileName, 'public');
        }

        if ($filePath) {
            SalesProof::create([
                'title' => $request->title,
                'description' => $request->description,
                'uploaded_by_employee_id' => $request->employee_id,
                'uploaded_by_admin_employee_id' => $loggedInEmployeeId,
                'file_path' => $filePath,
                'status' => 'pending',
            ]);

            return redirect()->route('sales-proofs.index')->with('success', 'Bukti penjualan berhasil diunggah!');
        }

        return back()->with('error', 'Gagal mengunggah bukti penjualan. File tidak ditemukan.');
    }

    /**
     * Menampilkan detail bukti penjualan tertentu.
     * Dapat diakses oleh owner, admin, administrator (sesuai route middleware).
     */
    public function show(SalesProof $salesProof)
    {
        $salesProof->load(['uploadedBy.user', 'validations.validatedBy.user']);

        // Tidak ada otorisasi tambahan di sini.
        // Route middleware sudah memastikan hanya owner, admin, administrator yang bisa akses.
        // Administrator memiliki akses penuh ke semua bukti penjualan yang bisa dia lihat.

        return view('sales-proofs.show', compact('salesProof'));
    }

    /**
     * Menampilkan form untuk mengedit bukti penjualan.
     * Hanya dapat diakses oleh administrator (sesuai route middleware).
     */
    public function edit(SalesProof $salesProof)
    {
        // Tidak ada otorisasi tambahan di sini.
        // Route middleware sudah memastikan hanya administrator yang bisa akses.
        // Administrator memiliki akses penuh ke semua bukti penjualan yang bisa dia edit.

        return view('sales-proofs.edit', compact('salesProof'));
    }

    /**
     * Memperbarui bukti penjualan di database.
     * Hanya dapat diakses oleh administrator (sesuai route middleware).
     */
    public function update(Request $request, SalesProof $salesProof)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'proof_file' => 'nullable|file|max:2048',
    ]);

    // Jika tidak ada input status dari user, pakai nilai lama
    $validated['status'] = $salesProof->status;

    // Handle file upload
    if ($request->hasFile('proof_file')) {
        $validated['file_path'] = $request->file('proof_file')->store('sales_proofs', 'public');
    }

    $salesProof->update($validated);

    return redirect()->route('sales-proofs.index')->with('success', 'Bukti penjualan berhasil diperbarui.');
}


    /**
     * Menghapus bukti penjualan dari database.
     * Hanya dapat diakses oleh administrator (sesuai route middleware).
     */
    public function destroy(SalesProof $salesProof)
    {
        // Tidak ada otorisasi tambahan di sini.
        // Route middleware sudah memastikan hanya administrator yang bisa akses.
        // Administrator memiliki akses penuh ke semua bukti penjualan yang bisa dia hapus.

        if ($salesProof->file_path && Storage::disk('public')->exists($salesProof->file_path)) {
            Storage::disk('public')->delete($salesProof->file_path);
        }
        $salesProof->delete();
        return redirect()->route('sales-proofs.index')->with('success', 'Bukti penjualan berhasil dihapus!');
    }

    /**
     * Menampilkan laporan penjualan yang sudah tervalidasi.
     */
    public function salesReportIndex()
    {
        $validatedSalesProofs = SalesProof::where('status', 'validated')
                                         ->with(['uploadedBy.user', 'validations.validatedBy.user'])
                                         ->orderBy('created_at', 'desc') // Menggunakan created_at dari SalesProof
                                         ->get();

        return view('sales-reports.index', compact('validatedSalesProofs'));    }
}
