<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Untuk mengelola file
use Carbon\Carbon; // Untuk timestamp submission

class EmployeeTaskController extends Controller
{
    /**
     * Menampilkan daftar tugas yang diberikan kepada karyawan yang sedang login.
     */
    public function index()
    {
        /** @var \App\Models\Employee $employee */
        $employee = Auth::user(); // Karyawan yang sedang login

        // Ambil tugas-tugas yang diberikan kepada karyawan ini, eager load manager dan submission
        $tasks = $employee->receivedTasks()->with(['assignedBy', 'submissions'])->latest()->get();

        return view('employee-tasks.index', compact('tasks'));
    }

    /**
     * Menampilkan detail tugas tertentu untuk karyawan.
     */
    public function show(Task $task)
    {
        /** @var \App\Models\Employee $employee */
        $employee = Auth::user(); // Karyawan yang sedang login

        // Pastikan karyawan hanya bisa melihat tugas yang ditugaskan kepadanya
        if ($task->assigned_to_employee_id !== $employee->id) {
            abort(403, 'Unauthorized action.'); // Akses ditolak
        }

        // Eager load relasi manager yang memberikan tugas, dan submission yang sudah ada
        $task->load(['assignedBy', 'submissions' => function($query) use ($employee) {
            $query->where('submitted_by_employee_id', $employee->id); // Hanya submission dari karyawan ini
        }]);

        // Cek apakah tugas ini sudah pernah dikumpulkan oleh karyawan ini
        $hasSubmitted = $task->submissions->isNotEmpty();

        return view('employee-tasks.show', compact('task', 'hasSubmitted'));
    }

    /**
     * Menampilkan form untuk mengirimkan tugas (submission).
     */
    public function showSubmitForm(Task $task)
    {
        /** @var \App\Models\Employee $employee */
        $employee = Auth::user(); // Karyawan yang sedang login

        // Pastikan karyawan hanya bisa submit tugas yang ditugaskan kepadanya
        if ($task->assigned_to_employee_id !== $employee->id) {
            abort(403, 'Unauthorized action.');
        }

        // Cek apakah tugas sudah pernah dikumpulkan (jika hanya boleh sekali)
        $existingSubmission = TaskSubmission::where('task_id', $task->id)
                                            ->where('submitted_by_employee_id', $employee->id)
                                            ->first();

        if ($existingSubmission) {
            return redirect()->route('employee-tasks.show', $task)->with('error', 'Anda sudah mengumpulkan tugas ini.');
        }

        return view('employee-tasks.submit', compact('task'));
    }

    /**
     * Memproses pengiriman tugas (submission file).
     */
    public function submit(Request $request, Task $task)
    {
        /** @var \App\Models\Employee $employee */
        $employee = Auth::user(); // Karyawan yang sedang login

        // Pastikan karyawan hanya bisa submit tugas yang ditugaskan kepadanya
        if ($task->assigned_to_employee_id !== $employee->id) {
            abort(403, 'Unauthorized action.');
        }

        // Cek apakah tugas sudah pernah dikumpulkan (jika hanya boleh sekali)
        $existingSubmission = TaskSubmission::where('task_id', $task->id)
                                            ->where('submitted_by_employee_id', $employee->id)
                                            ->first();
        if ($existingSubmission) {
            return back()->with('error', 'Anda sudah mengumpulkan tugas ini.');
        }

        $request->validate([
            'submission_file' => 'required|file|mimes:pdf,doc,docx,zip,rar,ppt,pptx,xls,xlsx|max:20480', // Max 20MB
            'comments' => 'nullable|string|max:1000',
        ]);

        $filePath = null;
        if ($request->hasFile('submission_file')) {
            // Simpan file di direktori storage/app/public/submissions
            $file = $request->file('submission_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('submissions', $fileName, 'public'); // 'public' disk driver
        }

        if ($filePath) {
            TaskSubmission::create([
                'task_id' => $task->id,
                'submitted_by_employee_id' => $employee->id,
                'submission_file_path' => $filePath,
                'comments' => $request->comments,
                'submission_date' => Carbon::now(),
            ]);

            // Update status tugas menjadi 'submitted'
            $task->update(['status' => 'submitted']);

            return redirect()->route('employee-tasks.show', $task)->with('success', 'Tugas berhasil dikumpulkan!');
        }

        return back()->with('error', 'Gagal mengumpulkan tugas. File tidak ditemukan.');
    }
}