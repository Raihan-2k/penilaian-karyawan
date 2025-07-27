<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // Untuk bekerja dengan tanggal

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'schedule', // Ini akan di-cast ke array
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'schedule' => 'array', // Penting: cast kolom JSON ke array PHP
        'start_time' => 'datetime', // Cast ke objek Carbon
        'end_time' => 'datetime',   // Cast ke objek Carbon
    ];

    /**
     * Get the employees for the shift.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Check if a given date is a working day for this shift.
     *
     * @param Carbon $date
     * @return bool
     */
    public function isWorkingDay(Carbon $date): bool
    {
        // $date->dayOfWeek akan mengembalikan 0 (Minggu) sampai 6 (Sabtu)
        return in_array($date->dayOfWeek, $this->schedule['working_days'] ?? []);
    }

    /**
     * Check if a given date is an off day for this shift.
     *
     * @param Carbon $date
     * @return bool
     */
    public function isOffDay(Carbon $date): bool
    {
        return in_array($date->dayOfWeek, $this->schedule['off_days'] ?? []);
    }
}
