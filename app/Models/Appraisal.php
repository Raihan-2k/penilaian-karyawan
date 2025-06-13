<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appraisal extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'appraiser_id',
        'appraisal_date',
        'overall_feedback',
        'overall_score',
    ];

    protected $casts = [
        'appraisal_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function appraiser()
    {
        return $this->belongsTo(User::class, 'appraiser_id');
    }

    public function criterionScores()
    {
        return $this->hasMany(AppraisalCriterionScore::class);
    }
}
