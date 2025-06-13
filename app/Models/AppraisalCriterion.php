<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppraisalCriterion extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function appraisalCriterionScores()
    {
        return $this->hasMany(AppraisalCriterionScore::class);
    }
}