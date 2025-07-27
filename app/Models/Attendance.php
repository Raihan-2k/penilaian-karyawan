<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // Pastikan ini diimpor jika Anda menggunakannya di model (misal untuk accessor/mutator)

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'check_in_time',
        'check_out_time',
        'overtime_hours', // <--- Pastikan ini ADA jika Anda menyimpannya di DB
        'status',         // <--- Tambahkan ini jika Anda memiliki kolom 'status' di DB
        // Tambahkan kolom lain di sini jika ada dan ingin diisi secara massal
    ];

    protected $casts = [
        'date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
    ];

    /**
     * Get the employee that owns the attendance record.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Contoh accessor jika Anda ingin menampilkan status yang lebih user-friendly
    // public function getStatusAttribute($value)
    // {
    //     // Anda bisa menambahkan logika di sini untuk mengubah nilai 'status'
    //     // dari database menjadi format yang lebih mudah dibaca jika diperlukan.
    //     return ucfirst($value);
    // }
}
