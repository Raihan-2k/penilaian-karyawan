<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\Appraisal;
use App\Models\Attendance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        $employeeData = null;
        if ($user->employee) {
            $employeeData = $user->employee;
        }

        $totalEmployees = Employee::count();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $appraisalsThisMonth = Appraisal::whereBetween('appraisal_date', [$startOfMonth, $endOfMonth])
                                         ->count();

        $today = Carbon::today();
        $employeesCheckedInToday = Attendance::whereDate('check_in_time', $today)->pluck('employee_id');
        $employeesNotCheckedInToday = Employee::whereNotIn('id', $employeesCheckedInToday)->count();

        $recentAppraisals = Appraisal::with(['employee.user', 'appraiser.user'])
                                     ->orderBy('appraisal_date', 'desc')
                                     ->limit(5)
                                     ->get();

        $recentAttendances = Attendance::with('employee.user')
                                       ->orderBy('check_in_time', 'desc')
                                       ->limit(5)
                                       ->get();

        return view('dashboard', compact(
            'user',
            'employeeData',
            'totalEmployees',
            'appraisalsThisMonth',
            'employeesNotCheckedInToday',
            'recentAppraisals',
            'recentAttendances'
        ));
    }
}
