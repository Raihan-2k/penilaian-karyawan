<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'submitted_by_employee_id',
        'submission_file_path',
        'comments',
        'submission_date',
    ];

    protected $casts = [
        'submission_date' => 'datetime',
    ];

    // Relasi: Tugas yang terkait dengan submission ini
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // Relasi: Karyawan yang melakukan submission ini
    public function submittedBy()
    {
        return $this->belongsTo(Employee::class, 'submitted_by_employee_id');
    }
}