<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesValidation extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_proof_id',
        'validated_by_employee_id', // <--- PERBAIKAN DI SINI
        'status',
        'comments',
        'validated_at',
    ];

    protected $casts = [
        'validated_at' => 'datetime',
    ];

    public function salesProof()
    {
        return $this->belongsTo(SalesProof::class);
    }

    public function validatedBy()
    {
        // Relasi ini sekarang menunjuk ke 'validated_by_employee_id'
        return $this->belongsTo(Employee::class, 'validated_by_employee_id'); // <--- PERBAIKAN DI SINI
    }
}
