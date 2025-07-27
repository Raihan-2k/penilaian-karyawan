<?php

namespace App\Http\Controllers;

use App\Models\SalesProof;
use App\Models\SalesValidation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesValidationController extends Controller
{
    /**
     * Constructor untuk menerapkan middleware role (hanya manager).
     */
    public function __construct()
    {
        $this->middleware('role:manager');
    }

    /**
     * Display a listing of the resource (Sales Proofs awaiting validation).
     * Pastikan metode ini ada dan benar.
     */
    public function index()
    {
        // Ambil semua bukti penjualan yang statusnya 'pending'
        // Eager load relasi 'uploadedBy' (Employee) dan 'user' dari uploadedBy
        $salesProofs = SalesProof::where('status', 'pending')
                                 ->with('uploadedBy.user')
                                 ->orderBy('created_at', 'desc')
                                 ->paginate(10); // Atau get() jika tidak ingin pagination

        return view('sales-validations.index', compact('salesProofs'));
    }

    /**
     * Show the form for validating a specific sales proof.
     */
    public function showValidationForm(SalesProof $salesProof)
    {
        // Pastikan hanya bukti penjualan yang 'pending' yang bisa divalidasi
        if ($salesProof->status !== 'pending') {
            return redirect()->route('sales-validations.index')->with('error', 'Bukti penjualan ini sudah divalidasi atau ditolak.');
        }

        $salesProof->load('uploadedBy.user');
        return view('sales-validations.create', compact('salesProof'));
    }

    /**
     * Store a newly created resource in storage (perform validation).
     */
    public function store(Request $request)
    {
        $request->validate([
            'sales_proof_id' => 'required|exists:sales_proofs,id',
            'status' => 'required|in:validated,rejected',
            'notes' => 'nullable|string|max:500',
        ]);

        $salesProof = SalesProof::findOrFail($request->sales_proof_id);

        if ($salesProof->status !== 'pending') {
            return redirect()->route('sales-validations.index')->with('error', 'Bukti penjualan ini sudah divalidasi atau ditolak.');
        }

        /** @var \App\Models\User $loggedInUser */
        $loggedInUser = Auth::user();
        $validatedByEmployeeId = $loggedInUser->employee ? $loggedInUser->employee->id : null;

        if (!$validatedByEmployeeId) {
            return back()->with('error', 'Profil karyawan Anda tidak ditemukan. Anda tidak dapat melakukan validasi.');
        }

        $salesProof->status = $request->status;
        $salesProof->save();

        SalesValidation::create([
            'sales_proof_id' => $salesProof->id,
            'validated_by_employee_id' => $validatedByEmployeeId, // Menggunakan nama kolom yang benar
            'status' => $request->status,
            'comments' => $request->notes,
            'validated_at' => now(),
        ]);

        return redirect()->route('sales-validations.index')->with('success', 'Bukti penjualan berhasil divalidasi.');
    }
}
