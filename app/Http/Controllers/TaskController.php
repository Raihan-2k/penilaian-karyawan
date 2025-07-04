<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Employee;
use Illuminate\Http\Request; // Koreksi: use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Koreksi: use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Validation\Rule; // Koreksi: use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    /**
     * Menampilkan daftar tugas yang diberikan oleh Manager yang sedang login.
     */
    public function index()
    {
        /** @var \App\Models\Employee $manager */
        $manager = Auth::user();

        // Ambil tugas-tugas yang diberikan oleh manager ini, eager load karyawan terkait
        $tasks = $manager->assignedTasks()->with(['assignedTo', 'submissions'])->latest()->get();

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Menampilkan form untuk membuat tugas baru.
     */
    public function create()
    {
        // Ambil semua karyawan yang memiliki role 'karyawan' untuk dropdown
        $employees = Employee::where('role', 'karyawan')->orderBy('name')->get();

        return view('tasks.create', compact('employees'));
    }

    /**
     * Menyimpan tugas baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to_employee_id' => 'required|exists:employees,id',
            'deadline' => 'nullable|date|after_or_equal:today',
        ]);

        /** @var \App\Models\Employee $manager */
        $manager = Auth::user();

        Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'assigned_to_employee_id' => $request->assigned_to_employee_id,
            'assigned_by_manager_id' => $manager->id,
            'deadline' => $request->deadline,
            'status' => 'pending',
        ]);

        return redirect()->route('tasks.index')->with('success', 'Tugas berhasil diberikan!');
    }

    /**
     * Menampilkan detail tugas tertentu.
     */
    public function show(Task $task)
    {
        $task->load(['assignedTo', 'assignedBy', 'submissions.submittedBy']);

        if ($task->assigned_by_manager_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('tasks.show', compact('task'));
    }

    /**
     * Menampilkan form untuk mengedit tugas.
     */
    public function edit(Task $task)
    {
        if ($task->assigned_by_manager_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        $employees = Employee::where('role', 'karyawan')->orderBy('name')->get();
        return view('tasks.edit', compact('task', 'employees'));
    }

    /**
     * Memperbarui tugas di database.
     */
    public function update(Request $request, Task $task)
    {
        if ($task->assigned_by_manager_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to_employee_id' => 'required|exists:employees,id',
            'deadline' => 'nullable|date|after_or_equal:today',
            'status' => ['required', Rule::in(['pending', 'in_progress', 'completed', 'submitted'])],
        ]);

        $task->update($request->all());

        return redirect()->route('tasks.index')->with('success', 'Tugas berhasil diperbarui!');
    }

    /**
     * Menghapus tugas dari database.
     */
    public function destroy(Task $task)
    {
        if ($task->assigned_by_manager_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Tugas berhasil dihapus!');
    }
}
