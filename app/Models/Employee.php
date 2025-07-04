<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Employee extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'nip',
        'name',
        'email',
        'password',
        'remember_token',
        'email_verified_at',
        'must_change_password',
        'position',
        'role',
        'hire_date',
        'pendidikan_terakhir',
        'nomor_telepon',
        'tanggal_lahir',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'email_verified_at' => 'datetime',
        'must_change_password' => 'boolean',
        'tanggal_lahir' => 'date',
    ];

    // Relasi ke model Appraisal
    public function appraisals()
    {
        return $this->hasMany(Appraisal::class);
    }

    // Relasi ke model Attendance
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // --- Relasi Baru untuk Fitur Tugas ---

    // Relasi: Tugas yang diberikan oleh Employee ini (jika rolenya manager)
    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_by_manager_id');
    }

    // Relasi: Tugas yang diterima oleh Employee ini (jika rolenya karyawan)
    public function receivedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to_employee_id');
    }

    // Relasi: Submit tugas yang dilakukan oleh Employee ini
    public function taskSubmissions()
    {
        return $this->hasMany(TaskSubmission::class, 'submitted_by_employee_id');
    }
}   