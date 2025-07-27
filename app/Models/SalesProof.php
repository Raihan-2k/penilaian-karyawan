<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Koreksi: use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesProof extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'uploaded_by_employee_id',
        'file_path',
        'status',
    ];

    protected $casts = [
        //  'created_at' => 'datetime', // Already casted by default
        // 'updated_at' => 'datetime', // Already casted by default
    ];

    // Relasi: Karyawan (Administrator) yang mengunggah bukti penjualan ini
    public function uploadedBy()
    {
        return $this->belongsTo(Employee::class, 'uploaded_by_employee_id');
    }

    // Relasi: Validasi yang terkait dengan bukti penjualan ini
    public function validations()
    {
        return $this->hasMany(SalesValidation::class);
    }
}
