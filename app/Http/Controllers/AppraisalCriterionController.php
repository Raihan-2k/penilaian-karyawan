<?php

namespace App\Http\Controllers;

use App\Models\AppraisalCriterion; // Impor Model AppraisalCriterion
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Impor Rule untuk validasi unique saat update

class AppraisalCriterionController extends Controller
{
    /**
     * Menampilkan daftar semua kriteria penilaian.
     * Mengambil semua data kriteria dari database dan menampilkannya di view.
     */
    public function index()
    {
        $criteria = AppraisalCriterion::all(); // Mengambil semua kriteria
        return view('appraisal-criteria.index', compact('criteria')); // Mengirim data ke view 'appraisal-criteria.index'
    }

    /**
     * Menampilkan formulir untuk membuat kriteria penilaian baru.
     */
    public function create()
    {
        return view('appraisal-criteria.create'); // Menampilkan view form tambah kriteria
    }

    /**
     * Menyimpan kriteria penilaian baru ke database.
     * Memvalidasi input dan membuat record baru di tabel 'appraisal_criteria'.
     */
    public function store(Request $request)
    {
        // Validasi input dari formulir
        $request->validate([
            'name' => 'required|string|max:255|unique:appraisal_criteria,name', // Nama wajib, unik
            'description' => 'nullable|string', // Deskripsi opsional
        ]);

        // Membuat record kriteria baru di database
        AppraisalCriterion::create($request->all());

        // Redirect kembali ke halaman daftar kriteria dengan pesan sukses
        return redirect()->route('appraisal-criteria.index')->with('success', 'Kriteria penilaian berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail kriteria penilaian tertentu.
     * Dalam konteks ini, mungkin tidak selalu perlu halaman detail terpisah
     * jika informasi detail sudah terlihat di halaman index atau edit.
     * Metode ini disediakan karena ini adalah resource controller standar.
     */
    public function show(AppraisalCriterion $appraisalCriterion)
    {
        // Anda bisa membuat view 'appraisal-criteria.show' jika diperlukan.
        // Untuk kesederhanaan, Anda bisa redirect ke halaman edit atau index.
        return view('appraisal-criteria.show', compact('appraisalCriterion'));
    }

    /**
     * Menampilkan formulir untuk mengedit kriteria penilaian yang sudah ada.
     * Menggunakan Route Model Binding untuk mendapatkan objek AppraisalCriterion.
     */
    public function edit(AppraisalCriterion $appraisalCriterion)
    {
        return view('appraisal-criteria.edit', compact('appraisalCriterion')); // Menampilkan view form edit kriteria
    }

    /**
     * Memperbarui data kriteria penilaian di database.
     * Memvalidasi input dan memperbarui record yang sudah ada.
     */
    public function update(Request $request, AppraisalCriterion $appraisalCriterion)
    {
        // Validasi input dari formulir, dengan pengecualian untuk nama kriteria yang sedang diedit
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('appraisal_criteria', 'name')->ignore($appraisalCriterion->id)],
            'description' => 'nullable|string',
        ]);

        // Memperbarui record kriteria di database
        $appraisalCriterion->update($request->all());

        // Redirect kembali ke halaman daftar kriteria dengan pesan sukses
        return redirect()->route('appraisal-criteria.index')->with('success', 'Kriteria penilaian berhasil diperbarui!');
    }

    /**
     * Menghapus kriteria penilaian dari database.
     * Menggunakan Route Model Binding.
     * Akan menghapus juga skor kriteria yang terkait karena relasi onDelete('cascade').
     */
    public function destroy(AppraisalCriterion $appraisalCriterion)
    {
        $appraisalCriterion->delete(); // Menghapus record kriteria dari database
        // Redirect kembali ke halaman daftar kriteria dengan pesan sukses
        return redirect()->route('appraisal-criteria.index')->with('success', 'Kriteria penilaian berhasil dihapus!');
    }
}