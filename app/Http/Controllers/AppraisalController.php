<?php

namespace App\Http\Controllers;

use App\Models\Appraisal;
use App\Models\Employee;
use App\Models\AppraisalCriterion;
use App\Models\AppraisalCriterionScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Pastikan ini diimpor jika menggunakan Carbon di controller

class AppraisalController extends Controller
{
    /**
     * Menampilkan daftar semua penilaian.
     */
    public function index()
    {
        // Ambil semua penilaian, eager load relasi employee dan appraiser untuk tampilan
        // Pastikan Anda punya relasi 'employee' dan 'appraiser' di model Appraisal.php
        $appraisals = Appraisal::with(['employee', 'appraiser'])->latest()->get();
        return view('appraisals.index', compact('appraisals'));
    }

    /**
     * Menampilkan form untuk membuat penilaian baru.
     */
    public function create()
    {
        $employees = Employee::orderBy('name')->get(); // Ambil semua karyawan untuk dropdown
        $criteria = AppraisalCriterion::orderBy('name')->get(); // Ambil semua kriteria penilaian

        return view('appraisals.create', compact('employees', 'criteria'));
    }

    /**
     * Menyimpan penilaian baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi input utama penilaian
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'appraisal_date' => 'required|date',
            'overall_feedback' => 'nullable|string',
            // Validasi untuk setiap skor kriteria.
            'scores' => 'required|array',
            'scores.*.criterion_id' => 'required|exists:appraisal_criteria,id',
            'scores.*.score' => 'required|integer|min:1|max:5', // Skor harus angka 1-5
            'scores.*.comments' => 'nullable|string',
        ]);

        // Gunakan transaksi database untuk memastikan data tersimpan semua atau tidak sama sekali
        DB::transaction(function () use ($request) {
            // 1. Simpan data penilaian utama
            $appraisal = Appraisal::create([
                'employee_id' => $request->employee_id,
                'appraiser_id' => Auth::id(), // ID user yang sedang login sebagai penilai
                'appraisal_date' => $request->appraisal_date,
                'overall_feedback' => $request->overall_feedback,
                // Anda bisa menghitung overall_score di sini jika mau
                // 'overall_score' => $this->calculateOverallScore($request->scores),
            ]);

            // 2. Simpan skor untuk setiap kriteria
            foreach ($request->scores as $scoreData) {
                AppraisalCriterionScore::create([
                    'appraisal_id' => $appraisal->id,
                    'appraisal_criterion_id' => $scoreData['criterion_id'],
                    'score' => $scoreData['score'],
                    'comments' => $scoreData['comments'],
                ]);
            }
        });

        return redirect()->route('appraisals.index')->with('success', 'Penilaian berhasil disimpan!');
    }

    /**
     * Menampilkan detail penilaian tertentu.
     */
    public function show(Appraisal $appraisal)
    {
        // Eager load relasi employee, appraiser, dan criterionScores (dengan kriteria)
        $appraisal->load(['employee', 'appraiser', 'criterionScores.criterion']);
        return view('appraisals.show', compact('appraisal'));
    }

    // Metode edit dan update tidak diimplementasikan di versi sederhana ini.
    // Metode destroy juga tidak diimplementasikan di versi sederhana ini.
    public function edit(Appraisal $appraisal) { /* Not implemented yet */ }
    public function update(Request $request, Appraisal $appraisal) { /* Not implemented yet */ }
    public function destroy(Appraisal $appraisal) { /* Not implemented yet */ }
}