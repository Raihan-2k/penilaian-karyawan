<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'assigned_to_employee_id',
        'assigned_by_manager_id',
        'deadline',
        'status',
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    // Relasi: Karyawan yang menerima tugas ini
    public function assignedTo()
    {
        return $this->belongsTo(Employee::class, 'assigned_to_employee_id');
    }

    // Relasi: Manager yang memberikan tugas ini
    public function assignedBy()
    {
        return $this->belongsTo(Employee::class, 'assigned_by_manager_id');
    }

    // Relasi: Submit tugas yang terkait dengan tugas ini (satu tugas bisa punya banyak submission jika diizinkan)
    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class);
    }
}