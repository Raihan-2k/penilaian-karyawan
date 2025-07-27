<?php

namespace App\Http\Controllers;

use App\Models\AppraisalCriterion;
use Illuminate\Http\Request; // Koreksi: use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Koreksi: use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class AppraisalCriterionController extends Controller
{
    public function __construct()
    {
        // Hanya role 'admin' atau 'administrator' yang diizinkan mengakses controller ini
        // Manager tidak bisa akses ini
        $this->middleware('role:admin,administrator,owner'); // <--- TAMBAHKAN 'owner'
    }

    public function index()
    {
        $criteria = AppraisalCriterion::all();
        return view('appraisal-criteria.index', compact('criteria'));
    }

    public function create()
    {
        return view('appraisal-criteria.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:appraisal_criteria,name',
            'description' => 'nullable|string',
        ]);

        AppraisalCriterion::create($request->all());

        return redirect()->route('appraisal-criteria.index')->with('success', 'Kriteria penilaian berhasil ditambahkan!');
    }

    public function show(AppraisalCriterion $appraisalCriterion)
    {
        return view('appraisal-criteria.show', compact('appraisalCriterion'));
    }

    public function edit(AppraisalCriterion $appraisalCriterion)
    {
        return view('appraisal-criteria.edit', compact('appraisalCriterion'));
    }

    public function update(Request $request, AppraisalCriterion $appraisalCriterion)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('appraisal_criteria', 'name')->ignore($appraisalCriterion->id)],
            'description' => 'nullable|string',
        ]);

        $appraisalCriterion->update($request->all());

        return redirect()->route('appraisal-criteria.index')->with('success', 'Kriteria penilaian berhasil diperbarui!');
    }

    public function destroy(AppraisalCriterion $appraisalCriterion)
    {
        $appraisalCriterion->delete();
        return redirect()->route('appraisal-criteria.index')->with('success', 'Kriteria penilaian berhasil dihapus!');
    }
}
