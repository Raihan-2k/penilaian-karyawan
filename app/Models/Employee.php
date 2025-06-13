<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'nip',
        'name',
        'position',
        'hire_date'
    ];

    // Tambahkan bagian ini
    protected $casts = [
        'hire_date' => 'date', // Ini akan otomatis mengubah hire_date menjadi objek Carbon
    ];

    public function appraisals()
    {
        return $this->hasMany(Appraisal::class);
    }
     public function loginAccount()
    {
        return $this->hasOne(EmployeeLogin::class);
    }

    // Relasi untuk data absensi
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}