<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee; // Untuk filter karyawan
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminAttendanceController extends Controller
{
    public function index(Request $request)
    {
        // Mendapatkan tanggal dari request, default hari ini
        $date = $request->input('date', Carbon::today()->toDateString());
        $employeeId = $request->input('employee_id');

        $attendances = Attendance::with('employee') // Load relasi karyawan
                                ->whereDate('date', $date);

        if ($employeeId) {
            $attendances->where('employee_id', $employeeId);
        }

        $attendances = $attendances->orderBy('check_in_time', 'asc')->get();

        $employees = Employee::orderBy('name')->get(); // Untuk dropdown filter

        return view('admin.attendances.index', compact('attendances', 'date', 'employees', 'employeeId'));
    }

    // Anda bisa tambahkan metode edit, update, delete, atau approval lembur di sini
    // public function edit(Attendance $attendance) { ... }
    // public function update(Request $request, Attendance $attendance) { ... }
    // public function destroy(Attendance $attendance) { ... }
    // public function approveOvertime(Attendance $attendance) { ... }
}