<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appraisal extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'appraiser_id', // ID karyawan yang melakukan penilaian (sekarang ini adalah ID dari Employee)
        'appraisal_date',
        'overall_feedback',
        'overall_score',
    ];

    protected $casts = [
        'appraisal_date' => 'date',
    ];

    // Relasi ke model Employee (karyawan yang dinilai)
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    // Relasi ke model Employee (user yang melakukan penilaian)
    // PENTING: relasi ini harus menunjuk ke model Employee
    public function appraiser()
    {
        return $this->belongsTo(Employee::class, 'appraiser_id');
    }

    // Relasi ke AppraisalCriterionScore (skor kriteria untuk penilaian ini)
    public function criterionScores()
    {
        return $this->hasMany(AppraisalCriterionScore::class);
    }
}