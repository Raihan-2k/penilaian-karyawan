<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class AdminAttendanceController extends Controller
{
    protected $jamKerjaNormalPerHari = 8; // Atur jam kerja normal perusahaan di sini

    public function index(Request $request)
    {
        $selectedDate = Carbon::parse($request->input('date', Carbon::today()->toDateString()));
        $employeeIdFilter = $request->input('employee_id');

        $allEmployees = Employee::orderBy('name')->get();
        $query = Employee::query();

        if ($employeeIdFilter) {
            // Apply filter directly to the query builder before getting results
            $query->where('id', (int)$employeeIdFilter);
        }

        $employees = $query->get(); // Execute the query to get filtered employees

        // Dapatkan daftar hari libur jika fiturnya aktif, jika tidak, kosongkan array.
        // if (class_exists(Holiday::class)) { // Cek apakah model Holiday ada
        //     $holidays = Holiday::whereYear('date', $selectedDate->year)->get()->keyBy(function ($item) {
        //         return $item->date->format('Y-m-d');
        //     });
        // } else {
            $holidays = collect(); // Kosongkan jika model Holiday tidak ada
        // }


        $attendanceData = [];

        foreach ($employees as $employee) {
            $status = '';
            $checkInTime = null;
            $checkOutTime = null;
            $totalWorkHours = 0; // Inisialisasi untuk tampilan
            $overtimeHours = 0; // Inisialisasi untuk tampilan
            $attendanceRecord = null;

            $attendanceRecord = Attendance::where('employee_id', $employee->id)
                                          ->where('date', $selectedDate->toDateString())
                                          ->first();

            if ($attendanceRecord) {
                // Cek jika record ada tapi waktu null (kasus di-override jadi absen/libur)
                if ($attendanceRecord->check_in_time === null && $attendanceRecord->check_out_time === null) {
                    $status = 'Absen'; // Record ada tapi waktu null -> manager override jadi absen
                    $totalWorkHours = 0;
                    $overtimeHours = 0;
                } else {
                    // Record ada dan ada waktunya
                    $status = 'Hadir';
                    $checkInTime = $attendanceRecord->check_in_time; // Sudah Carbon karena di-cast di model
                    $checkOutTime = $attendanceRecord->check_out_time; // Sudah Carbon karena di-cast di model
                    $overtimeHours = $attendanceRecord->overtime_hours; // Ambil dari DB (yang seharusnya sudah benar)

                    // Hitung total jam kerja untuk tampilan (dari waktu yang tercatat)
                    if ($checkInTime && $checkOutTime) { // Pastikan keduanya ada sebelum hitung durasi
                        $totalWorkDuration = abs($checkOutTime->diffInMinutes($checkInTime));
                        $totalWorkHours = round($totalWorkDuration / 60, 2);
                    }
                }

            } else {
                // Jika TIDAK ada record absensi sama sekali (kondisi asli absen/hadir otomatis/libur)
                if ($selectedDate->isWeekend()) {
                    $status = 'Libur Akhir Pekan';
                }
                // Hapus pengecekan Holiday di sini jika fiturnya sudah dihapus
                // elseif ($holidays->has($selectedDate->toDateString())) {
                //     $status = 'Libur Nasional';
                // }
                else {
                    if ($employee->role === 'manager') {
                        $status = 'Hadir ';
                    } else {
                        $status = 'Tidak Hadir';
                    }
                }
            }

            $attendanceData[] = [
                'employee' => $employee,
                'date' => $selectedDate,
                'status' => $status,
                'check_in_time' => $checkInTime,
                'check_out_time' => $checkOutTime,
                'total_work_hours' => $totalWorkHours, // Pastikan ini dikirim untuk display
                'overtime_hours' => $overtimeHours, // Pastikan ini dikirim untuk display
                'record' => $attendanceRecord // Kirim record lengkap (bisa null)
            ];
        }

        // $allEmployees sudah diambil di awal method, tidak perlu query ulang
        // $allEmployees = Employee::orderBy('name')->get(); 

        return view('admin.attendances.index', compact(
            'attendanceData',
            'selectedDate',
            'allEmployees', // Menggunakan $allEmployees yang sudah ada
            'employeeIdFilter'
        ));
    }

    /**
     * Menampilkan form untuk edit atau membuat record absensi baru secara manual.
     * Menerima Employee $employee dan tanggal sebagai parameter.
     */
    public function editOrCreate(Employee $employee, string $date)
    {
        $date = Carbon::parse($date);

        $attendance = Attendance::where('employee_id', $employee->id)
                                ->where('date', $date->toDateString())
                                ->first();

        $check_in_time_str = null;
        $check_out_time_str = null;
        $initialStatus = '';

        if (!$attendance) {
            $attendance = new Attendance();
            $attendance->employee_id = $employee->id;
            $attendance->date = $date;

            // --- NILAI DEFAULT UNTUK FORM BARU (Waktu Saat Ini) ---
            $check_in_time_str = Carbon::now()->format('H:i'); // Default waktu check-in saat ini
            $check_out_time_str = Carbon::now()->format('H:i'); // Default waktu check-out saat ini
            // --- AKHIR PENAMBAHAN ---

            // Tentukan status awal untuk form baru
            if ($date->isWeekend()) {
                $initialStatus = 'Libur Akhir Pekan';
            }
            // Jika fitur Holiday diaktifkan:
            // elseif (class_exists(Holiday::class) && Holiday::where('date', $date->toDateString())->exists()) {
            //     $initialStatus = 'Libur Nasional';
            // }
            else {
                // Default status jika tidak ada record dan bukan hari libur
                $initialStatus = 'Absen';
                if ($employee->role === 'manager') {
                    $initialStatus = 'Hadir (Otomatis)';
                }
            }

        } else {
            // Jika record ditemukan, format waktu untuk form input
            $check_in_time_str = $attendance->check_in_time ? $attendance->check_in_time->format('H:i') : Carbon::now()->format('H:i');
            $check_out_time_str = $attendance->check_out_time ? $attendance->check_out_time->format('H:i') : Carbon::now()->format('H:i');

            // Tentukan status awal berdasarkan ada/tidaknya waktu check-in/out
            if ($attendance->check_in_time === null && $attendance->check_out_time === null) {
                $initialStatus = 'Absen'; // Record ada tapi waktu null, berarti diabsenkan
            } else {
                $initialStatus = 'Hadir'; // Record ada dan ada waktunya
            }
        }

        return view('admin.attendances.manual_entry', compact('employee', 'date', 'attendance', 'check_in_time_str', 'check_out_time_str', 'initialStatus'));
    }

    /**
     * Memperbarui record absensi yang sudah ada di database.
     * Metode ini akan dipanggil oleh form jika record_id sudah ada.
     */
    public function update(Request $request, Attendance $attendance)
    {
        $rules = [
            'status_manual' => ['required', 'string', Rule::in(['Hadir', 'Absen', 'Libur Akhir Pekan', 'Libur Nasional'])],
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i|after_or_equal:check_in_time',
        ];

        // Jika statusnya Hadir, waktu check-in/out wajib diisi
        if ($request->status_manual === 'Hadir') {
            $rules['check_in_time'] = 'required|date_format:H:i';
            $rules['check_out_time'] = 'required|date_format:H:i|after_or_equal:check_in_time';
        }

        $request->validate($rules);

        $checkInTime = $request->check_in_time;
        $checkOutTime = $request->check_out_time;
        $statusManual = $request->status_manual;

        $overtimeHours = 0; // Inisialisasi
        $totalWorkDuration = 0; // Inisialisasi

        // Logika override status
        if ($statusManual === 'Absen' || $statusManual === 'Libur Akhir Pekan' || $statusManual === 'Libur Nasional') {
            // Jika status diubah menjadi Absen/Libur, set waktu menjadi null dan overtime 0
            $checkInTime = null;
            $checkOutTime = null;
            $overtimeHours = 0;
            $totalWorkDuration = 0; // Pastikan ini juga direset
        } else { // Status Hadir
            if ($checkInTime && $checkOutTime) {
                $start = Carbon::parse($attendance->date->toDateString() . ' ' . $checkInTime);
                $end = Carbon::parse($attendance->date->toDateString() . ' ' . $checkOutTime);
                $totalWorkDuration = abs($end->diffInMinutes($start)); // Pastikan abs()
                $overtimeHours = $this->calculateOvertimeHours($totalWorkDuration);
            } else {
                // Jika status Hadir tapi waktu tidak lengkap, ini seharusnya dicegah oleh validasi required di atas.
                // Tapi sebagai fallback, pastikan overtime 0.
                $overtimeHours = 0;
                $totalWorkDuration = 0; // Pastikan ini juga direset
            }
        }

        $attendance->update([
            'check_in_time' => $checkInTime,
            'check_out_time' => $checkOutTime,
            'overtime_hours' => $overtimeHours, // <-- Pastikan ini tidak dikomentari
        ]);

        // Tambahkan pesan info jika ada perubahan status ke Absen/Libur
        $successMessage = 'Absensi berhasil diperbarui!';
        if ($statusManual !== 'Hadir') {
            $successMessage .= ' Status diubah menjadi ' . $statusManual . '.';
        }

        return redirect()->route('admin.attendances.index', ['date' => $attendance->date->format('Y-m-d')])->with('success', $successMessage);
    }

    /**
     * Menyimpan record absensi baru secara manual.
     * Metode ini akan dipanggil oleh form jika ini adalah record baru.
     */
    public function storeManual(Request $request)
    {
        $rules = [
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'status_manual' => ['required', 'string', Rule::in(['Hadir', 'Absen', 'Libur Akhir Pekan', 'Libur Nasional'])],
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i|after_or_equal:check_in_time',
        ];

        // Jika statusnya Hadir, waktu check-in/out wajib diisi
        if ($request->status_manual === 'Hadir') {
            $rules['check_in_time'] = 'required|date_format:H:i';
            $rules['check_out_time'] = 'required|date_format:H:i|after_or_equal:check_in_time';
        }

        $request->validate($rules);

        $date = Carbon::parse($request->date);
        $checkInTime = $request->check_in_time;
        $checkOutTime = $request->check_out_time;
        $statusManual = $request->status_manual;

        // Cek apakah record sudah ada untuk tanggal ini
        $existingAttendance = Attendance::where('employee_id', $request->employee_id)
                                        ->where('date', $date->toDateString())
                                        ->first();

        if ($existingAttendance) {
            // Jika sudah ada, redirect ke form edit atau update saja
            return redirect()->route('admin.attendances.edit_or_create', ['employee' => $request->employee_id, 'date' => $date->format('Y-m-d')])->with('error', 'Record absensi sudah ada untuk tanggal ini. Silakan gunakan fitur edit.');
        }

        $overtimeHours = 0; // Inisialisasi
        $totalWorkDuration = 0; // Inisialisasi
        // Jika statusnya Hadir, hitung lembur
        if ($statusManual === 'Hadir') {
            if ($checkInTime && $checkOutTime) {
                $start = Carbon::parse($date->toDateString() . ' ' . $checkInTime);
                $end = Carbon::parse($date->toDateString() . ' ' . $checkOutTime);
                $totalWorkDuration = abs($end->diffInMinutes($start));
                $overtimeHours = $this->calculateOvertimeHours($totalWorkDuration);
            }
        } else {
            // Jika statusnya Absen/Libur, waktu check-in/out null dan overtime 0
            $checkInTime = null;
            $checkOutTime = null;
            $overtimeHours = 0;
            $totalWorkDuration = 0; // Pastikan ini juga direset
        }


        Attendance::create([
            'employee_id' => $request->employee_id,
            'date' => $date->toDateString(),
            'check_in_time' => $checkInTime,
            'check_out_time' => $checkOutTime,
            'overtime_hours' => $overtimeHours, // <-- Pastikan ini tidak dikomentari
        ]);

        return redirect()->route('admin.attendances.index', ['date' => $date->format('Y-m-d')])->with('success', 'Absensi manual berhasil disimpan!');
    }

    /**
     * Helper function to calculate overtime hours.
     * @param int $totalDurationInMinutes
     * @return float
     */
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
}
