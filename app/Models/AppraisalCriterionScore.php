<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppraisalCriterionScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'appraisal_id',
        'appraisal_criterion_id',
        'score',
        'comments',
    ];

    public function appraisal()
    {
        return $this->belongsTo(Appraisal::class);
    }

    public function criterion()
    {
        return $this->belongsTo(AppraisalCriterion::class, 'appraisal_criterion_id');
    }
}