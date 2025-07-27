<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use App\Models\Shift;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf; // PENTING: Impor facade PDF

class AdminAttendanceController extends Controller
{
    protected $jamKerjaNormalPerHari = 8;

    public function __construct()
    {
        $this->middleware('role:owner,admin,manager');
    }

    public function index(Request $request)
    {
        $selectedDate = Carbon::parse($request->input('date', Carbon::today()->toDateString()));
        $employeeIdFilter = $request->input('employee_id');

        $allEmployees = Employee::with(['user', 'shift'])->orderBy('name')->get();

        $query = Employee::with(['user', 'shift']);

        if ($employeeIdFilter) {
            $query->where('id', (int)$employeeIdFilter);
        }

        $employees = $query->get();

        $holidays = collect();

        $attendanceData = [];

        foreach ($employees as $employee) {
            $status = '';
            $checkInTime = null;
            $checkOutTime = null;
            $totalWorkHours = 0;
            $overtimeHours = 0;
            $attendanceRecord = null;

            $attendanceRecord = Attendance::where('employee_id', $employee->id)
                                         ->whereDate('check_in_time', $selectedDate)
                                         ->first();

            $isShiftOffDay = false;
            if ($employee->shift) {
                $isShiftOffDay = $employee->shift->isOffDay($selectedDate);
            }

            if ($attendanceRecord) {
                if ($attendanceRecord->check_in_time === null && $attendanceRecord->check_out_time === null) {
                    $status = 'Absen (Manual)';
                    $totalWorkHours = 0;
                    $overtimeHours = 0;
                } else {
                    $status = 'Hadir';
                    $checkInTime = $attendanceRecord->check_in_time;
                    $checkOutTime = $attendanceRecord->check_out_time;
                    $overtimeHours = $attendanceRecord->overtime_hours;

                    if ($checkInTime && $checkOutTime) {
                        $totalWorkDuration = abs($checkOutTime->diffInMinutes($checkInTime));
                        $totalWorkHours = round($totalWorkDuration / 60, 2);
                    }
                }
            } else {
                if ($isShiftOffDay) {
                    $status = 'Libur';
                }
                else {
                    $status = 'Absen';
                }
            }

            $attendanceData[] = [
                'employee' => $employee,
                'date' => $selectedDate,
                'status' => $status,
                'check_in_time' => $checkInTime,
                'check_out_time' => $checkOutTime,
                'total_work_hours' => $totalWorkHours,
                'overtime_hours' => $overtimeHours,
                'record' => $attendanceRecord
            ];
        }

        return view('admin.attendances.index', compact(
            'attendanceData',
            'selectedDate',
            'allEmployees',
            'employeeIdFilter'
        ));
    }

    /**
     * Menampilkan form untuk edit atau membuat record absensi baru secara manual.
     */
    public function editOrCreate(Employee $employee, string $date)
    {
        $employee->load(['user', 'shift']);

        $date = Carbon::parse($date);

        $attendance = Attendance::where('employee_id', $employee->id)
                                 ->whereDate('check_in_time', $date)
                                 ->first();

        $check_in_time_str = null;
        $check_out_time_str = null;
        $initialStatus = '';

        $isShiftOffDay = false;
        if ($employee->shift) {
            $isShiftOffDay = $employee->shift->isOffDay($date);
        }

        if (!$attendance) {
            $attendance = new Attendance();
            $attendance->employee_id = $employee->id;
            $attendance->date = $date;

            $check_in_time_str = Carbon::now()->format('H:i');
            $check_out_time_str = Carbon::now()->format('H:i');

            if ($isShiftOffDay) {
                $initialStatus = 'Libur';
            }
            else {
                $initialStatus = 'Absen';
            }

        } else {
            $check_in_time_str = $attendance->check_in_time ? $attendance->check_in_time->format('H:i') : null;
            $check_out_time_str = $attendance->check_out_time ? $attendance->check_out_time->format('H:i') : null;

            if ($attendance->check_in_time === null && $attendance->check_out_time === null) {
                $initialStatus = 'Absen';
            } else {
                $initialStatus = 'Hadir';
            }
        }

        return view('admin.attendances.manual_entry', compact('employee', 'date', 'attendance', 'check_in_time_str', 'check_out_time_str', 'initialStatus'));
    }

    /**
     * Memperbarui record absensi yang sudah ada di database.
     */
    public function update(Request $request, Attendance $attendance)
    {
        $rules = [
            'status_manual' => ['required', 'string', Rule::in(['Hadir', 'Absen', 'Libur', 'Libur Nasional'])],
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i|after_or_equal:check_in_time',
        ];

        if ($request->status_manual === 'Hadir') {
            $rules['check_in_time'] = 'required|date_format:H:i';
            $rules['check_out_time'] = 'required|date_format:H:i|after_or_equal:check_in_time';
        }

        $request->validate($rules);

        $checkInTime = $request->check_in_time ? Carbon::parse($attendance->date->toDateString() . ' ' . $request->check_in_time) : null;
        $checkOutTime = $request->check_out_time ? Carbon::parse($attendance->date->toDateString() . ' ' . $request->check_out_time) : null;
        $statusManual = $request->status_manual;

        $overtimeHours = 0;

        if (in_array($statusManual, ['Absen', 'Libur', 'Libur Nasional'])) {
            $checkInTime = null;
            $checkOutTime = null;
            $overtimeHours = 0;
        } else {
            if ($checkInTime && $checkOutTime) {
                $totalWorkDuration = abs($checkOutTime->diffInMinutes($checkInTime));
                $overtimeHours = $this->calculateOvertimeHours($totalWorkDuration);
            } else {
                $overtimeHours = 0;
            }
        }

        $attendance->update([
            'check_in_time' => $checkInTime,
            'check_out_time' => $checkOutTime,
            'overtime_hours' => $overtimeHours,
            'status' => $statusManual,
        ]);

        $successMessage = 'Absensi berhasil diperbarui!';
        if ($statusManual !== 'Hadir') {
            $successMessage .= ' Status diubah menjadi ' . $statusManual . '.';
        }

        return redirect()->route('admin.attendances.index', ['date' => $attendance->date->format('Y-m-d')])->with('success', $successMessage);
    }

    public function storeManual(Request $request)
    {
        $rules = [
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'status_manual' => ['required', 'string', Rule::in(['Hadir', 'Absen', 'Libur', 'Libur Nasional'])],
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i|after_or_equal:check_in_time',
        ];

        if ($request->status_manual === 'Hadir') {
            $rules['check_in_time'] = 'required|date_format:H:i';
            $rules['check_out_time'] = 'required|date_format:H:i|after_or_equal:check_in_time';
        }

        $request->validate($rules);

        $date = Carbon::parse($request->date);
        $checkInTime = $request->check_in_time;
        $checkOutTime = $request->check_out_time;
        $statusManual = $request->status_manual;

        $existingAttendance = Attendance::where('employee_id', $request->employee_id)
                                         ->whereDate('check_in_time', $date)
                                         ->first();

        if ($existingAttendance) {
            return redirect()->route('admin.attendances.edit_or_create', ['employee' => $request->employee_id, 'date' => $date->format('Y-m-d')])->with('error', 'Record absensi sudah ada untuk tanggal ini. Silakan gunakan fitur edit.');
        }

        $overtimeHours = 0;
        if ($statusManual === 'Hadir') {
            if ($checkInTime && $checkOutTime) {
                $start = Carbon::parse($date->toDateString() . ' ' . $checkInTime);
                $end = Carbon::parse($date->toDateString() . ' ' . $checkOutTime);
                $totalWorkDuration = abs($end->diffInMinutes($start));
                $overtimeHours = $this->calculateOvertimeHours($totalWorkDuration);
            }
        } else {
            $checkInTime = null;
            $checkOutTime = null;
            $overtimeHours = 0;
        }

        Attendance::create([
            'employee_id' => $request->employee_id,
            'date' => $date->toDateString(),
            'check_in_time' => $checkInTime,
            'check_out_time' => $checkOutTime,
            'overtime_hours' => $overtimeHours,
            'status' => $statusManual,
        ]);

        return redirect()->route('admin.attendances.index', ['date' => $date->format('Y-m-d')])->with('success', 'Absensi manual berhasil disimpan!');
    }

    /**
     * Menampilkan laporan absensi bulanan.
     */
    public function monthlyReport(Request $request): \Illuminate\View\View
    {
        $selectedYear = $request->input('year', Carbon::now()->year);
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $employeeIdFilter = $request->input('employee_id');

        $startDate = Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth();
        $endDate = Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth();

        $employeesQuery = Employee::with(['user', 'shift'])->orderBy('name');
        if ($employeeIdFilter) {
            $employeesQuery->where('id', (int)$employeeIdFilter);
        }
        $employees = $employeesQuery->get();

        $reportData = [];

        foreach ($employees as $employee) {
            $employeeReport = [
                'employee' => $employee,
                'total_work_hours' => 0,
                'total_overtime_hours' => 0,
                'days_present' => 0,
                'days_absent' => 0,
                'days_off' => 0,
                'daily_records' => []
            ];

            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                if ($currentDate->lt($employee->hire_date)) {
                    $status = 'Belum Bekerja';
                    $employeeReport['daily_records'][] = [
                        'date' => $currentDate->copy(),
                        'status' => $status,
                        'check_in' => null,
                        'check_out' => null,
                        'work_hours' => null,
                        'overtime_hours' => null,
                    ];
                    $currentDate->addDay();
                    continue;
                }

                $attendanceRecord = Attendance::where('employee_id', $employee->id)
                                             ->whereDate('check_in_time', $currentDate)
                                             ->first();

                $status = 'Absen';
                $dailyWorkHours = 0;
                $dailyOvertimeHours = 0;
                $checkIn = null;
                $checkOut = null;

                $isShiftOffDay = $employee->shift ? $employee->shift->isOffDay($currentDate) : false;

                if ($attendanceRecord) {
                    $status = $attendanceRecord->status;
                    $checkIn = $attendanceRecord->check_in_time;
                    $checkOut = $attendanceRecord->check_out_time;
                    $dailyOvertimeHours = $attendanceRecord->overtime_hours;

                    if ($checkIn && $checkOut) {
                        $duration = abs($checkOut->diffInMinutes($checkIn));
                        $dailyWorkHours = round($duration / 60, 2);
                        $employeeReport['total_work_hours'] += $dailyWorkHours;
                        $employeeReport['total_overtime_hours'] += $dailyOvertimeHours;
                        $employeeReport['days_present']++;
                    } else {
                        $employeeReport['days_absent']++;
                    }
                } else {
                    if ($isShiftOffDay) {
                        $status = 'Libur';
                        $employeeReport['days_off']++;
                    }
                    else {
                        $status = 'Absen';
                        $employeeReport['days_absent']++;
                    }
                }

                $employeeReport['daily_records'][] = [
                    'date' => $currentDate->copy(),
                    'status' => $status,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'work_hours' => $dailyWorkHours,
                    'overtime_hours' => $dailyOvertimeHours,
                ];

                $currentDate->addDay();
            }
            $reportData[] = $employeeReport;
        }

        $availableYears = range(Carbon::now()->year - 5, Carbon::now()->year + 1);
        $availableMonths = [];
        for ($m = 1; $m <= 12; $m++) {
            $availableMonths[$m] = Carbon::create(null, $m, 1)->translatedFormat('F');
        }

        $allEmployeesForFilter = Employee::with('user')->orderBy('name')->get();

        return view('admin.attendances.monthly_report', compact(
            'reportData',
            'selectedYear',
            'selectedMonth',
            'employeeIdFilter',
            'availableYears',
            'availableMonths',
            'allEmployeesForFilter'
        ));
    }

    protected function calculateOvertimeHours(int $totalDurationInMinutes): float
    {
        $totalWorkHours = $totalDurationInMinutes / 60;
        $overtimeHours = 0;

        if ($totalWorkHours > $this->jamKerjaNormalPerHari) {
            $overtimeHours = $totalWorkHours - $this->jamKerjaNormalPerHari;
            $overtimeHours = round($overtimeHours, 2);
        }

        return $overtimeHours;
    }

    /**
     * Mengekspor laporan absensi bulanan ke PDF.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportMonthlyReportPdf(Request $request)
    {
        $selectedYear = $request->input('year', Carbon::now()->year);
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $employeeIdFilter = $request->input('employee_id');

        // Reuse the logic from monthlyReport to get the data
        $startDate = Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth();
        $endDate = Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth();

        $employeesQuery = Employee::with(['user', 'shift'])->orderBy('name');
        if ($employeeIdFilter) {
            $employeesQuery->where('id', (int)$employeeIdFilter);
        }
        $employees = $employeesQuery->get();

        $reportData = [];

        foreach ($employees as $employee) {
            $employeeReport = [
                'employee' => $employee,
                'total_work_hours' => 0,
                'total_overtime_hours' => 0,
                'days_present' => 0,
                'days_absent' => 0,
                'days_off' => 0,
                'daily_records' => []
            ];

            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                // Cek jika tanggal saat ini sebelum tanggal masuk karyawan
                if ($currentDate->lt($employee->hire_date)) {
                    $status = 'Belum Bekerja';
                    $employeeReport['daily_records'][] = [
                        'date' => $currentDate->copy(),
                        'status' => $status,
                        'check_in' => null,
                        'check_out' => null,
                        'work_hours' => null,
                        'overtime_hours' => null,
                    ];
                    $currentDate->addDay();
                    continue;
                }

                $attendanceRecord = Attendance::where('employee_id', $employee->id)
                                             ->whereDate('check_in_time', $currentDate)
                                             ->first();

                $status = 'Absen';
                $dailyWorkHours = 0;
                $dailyOvertimeHours = 0;
                $checkIn = null;
                $checkOut = null;

                $isShiftOffDay = $employee->shift ? $employee->shift->isOffDay($currentDate) : false;

                if ($attendanceRecord) {
                    $status = $attendanceRecord->status;
                    $checkIn = $attendanceRecord->check_in_time;
                    $checkOut = $attendanceRecord->check_out_time;
                    $dailyOvertimeHours = $attendanceRecord->overtime_hours;

                    if ($checkIn && $checkOut) {
                        $duration = abs($checkOut->diffInMinutes($checkIn));
                        $dailyWorkHours = round($duration / 60, 2);
                        $employeeReport['total_work_hours'] += $dailyWorkHours;
                        $employeeReport['total_overtime_hours'] += $dailyOvertimeHours;
                        $employeeReport['days_present']++;
                    } else {
                        $employeeReport['days_absent']++;
                    }
                } else {
                    if ($isShiftOffDay) {
                        $status = 'Libur';
                        $employeeReport['days_off']++;
                    }
                    else {
                        $status = 'Absen';
                        $employeeReport['days_absent']++;
                    }
                }

                $employeeReport['daily_records'][] = [
                    'date' => $currentDate->copy(),
                    'status' => $status,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'work_hours' => $dailyWorkHours,
                    'overtime_hours' => $dailyOvertimeHours,
                ];

                $currentDate->addDay();
            }
            $reportData[] = $employeeReport;
        }

        // Nama file PDF
        $fileName = 'laporan_absensi_bulanan_' . $selectedMonth . '_' . $selectedYear;
        if ($employeeIdFilter) {
            $filteredEmployee = Employee::find($employeeIdFilter);
            if ($filteredEmployee && $filteredEmployee->user) {
                $fileName .= '_' . Str::slug($filteredEmployee->user->name);
            }
        }
        $fileName .= '.pdf';

        // Load view ke PDF dan stream
        $pdf = Pdf::loadView('admin.attendances.monthly_report_pdf', compact(
            'reportData',
            'selectedYear',
            'selectedMonth',
            'startDate', // Kirim startDate untuk header laporan
            'endDate'    // Kirim endDate untuk header laporan
        ));

        return $pdf->download($fileName);
    }
}
