<?php

namespace App\Http\Controllers;

use App\Models\Appraisal;
use App\Models\Employee;
use App\Models\AppraisalCriterion;
use App\Models\AppraisalCriterionScore;
use App\Models\User; // PENTING: Impor Model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class AppraisalController extends Controller
{
    /**
     * Constructor untuk menerapkan middleware role (owner/admin/manager).
     */
    public function __construct()
    {
        $this->middleware('role:owner,admin,manager,');
    }

    /**
     * Menampilkan daftar semua penilaian.
     * Memuat relasi 'employee' dan 'appraiser' beserta relasi 'user' mereka.
     */
    public function index()
    {
        // Eager load employee.user dan appraiser.user
        $appraisals = Appraisal::with(['employee.user', 'appraiser.user'])->latest()->get();
        return view('appraisals.index', compact('appraisals'));
    }

    /**
     * Menampilkan form untuk membuat penilaian baru.
     */
    public function create()
    {
        /** @var \App\Models\User $loggedInUser */
        $loggedInUser = Auth::user();

        // Dapatkan ID karyawan dari user yang login
        $loggedInEmployeeId = $loggedInUser->employee ? $loggedInUser->employee->id : null;

        if (!$loggedInEmployeeId) {
            abort(403, 'Anda tidak terdaftar sebagai karyawan dan tidak dapat membuat penilaian.');
        }

        // Ambil semua karyawan KECUALI karyawan yang sedang login
        // Eager load relasi 'user' untuk menampilkan nama pengguna yang benar
        $query = Employee::with('user')
                         ->where('id', '!=', $loggedInEmployeeId)
                         ->orderBy('name');

        if ($loggedInUser->role === 'manager') {
            $query->whereHas('user', function ($q) {
                $q->whereIn('role', ['karyawan', 'administrator']); // Manager hanya bisa menilai karyawan dan administrator
            });
        }
        $employees = $query->get();
        $criteria = AppraisalCriterion::orderBy('name')->get();

        return view('appraisals.create', compact('employees', 'criteria'));
    }

    /**
     * Menyimpan penilaian baru ke database.
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $loggedInUser */
        $loggedInUser = Auth::user();

        // Dapatkan ID karyawan dari user yang login (yang akan menjadi penilai)
        $appraiserEmployeeId = $loggedInUser->employee ? $loggedInUser->employee->id : null;

        if (!$appraiserEmployeeId) {
            abort(403, 'Anda tidak terdaftar sebagai karyawan dan tidak dapat membuat penilaian.');
        }

        $request->validate([
            'employee_id' => 'required|exists:employees,id', // Karyawan yang dinilai
            'appraisal_date' => 'required|date',
            'overall_feedback' => 'nullable|string',
            'scores' => 'required|array',
            'scores.*.criterion_id' => 'required|exists:appraisal_criteria,id',
            'scores.*.score' => ['required', 'integer', Rule::in([1, -1])],
            'scores.*.comments' => 'nullable|string',
        ]);

        // Periksa apakah karyawan yang dinilai sama dengan penilai
        // Menggunakan $request->employee_id (ID karyawan yang dinilai)
        // dan $appraiserEmployeeId (ID karyawan dari user yang login)
        if ($request->employee_id === $appraiserEmployeeId) {
            abort(403, 'Anda tidak dapat menilai diri sendiri.');
        }

         if ($loggedInUser->role === 'manager') {
            $employeeToAppraise = Employee::with('user')->find($request->employee_id);
            if (!$employeeToAppraise || !in_array($employeeToAppraise->user->role, ['karyawan', 'administrator'])) {
                abort(403, 'Manager hanya dapat menilai karyawan dengan peran Karyawan atau Administrator.');
            }
        }

        DB::transaction(function () use ($request, $appraiserEmployeeId) { // Lewatkan $appraiserEmployeeId ke closure
            $totalOverallScore = 0;
            foreach ($request->scores as $scoreData) {
                $totalOverallScore += (int)$scoreData['score'];
            }

            $appraisal = Appraisal::create([
                'employee_id' => $request->employee_id,
                'appraiser_id' => $appraiserEmployeeId, // Gunakan ID karyawan penilai
                'appraisal_date' => $request->appraisal_date,
                'overall_feedback' => $request->overall_feedback,
                'overall_score' => $totalOverallScore,
            ]);

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
     * Memuat relasi 'employee', 'appraiser', dan 'criterionScores' beserta relasi 'user' mereka.
     */
    public function show(Appraisal $appraisal)
    {
        // Eager load employee.user, appraiser.user, dan criterionScores.criterion
        $appraisal->load(['employee.user', 'appraiser.user', 'criterionScores.criterion']);
        return view('appraisals.show', compact('appraisal'));
    }

    // Metode edit, update, destroy tetap abort(404) jika belum diimplementasikan
    public function edit(Appraisal $appraisal)
    {
        abort(404); // Not Implemented
    }

    public function update(Request $request, Appraisal $appraisal)
    {
        abort(404); // Not Implemented
    }

    public function destroy(Appraisal $appraisal)
    {
        abort(404); // Not Implemented
    }
}
