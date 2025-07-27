<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    // Pastikan fillable sudah sesuai dengan semua kolom yang ingin diisi secara massal
    protected $fillable = [
        'user_id', // Foreign key ke tabel users
        'nip', // Tambahkan ini
        'position', // Tambahkan ini
        'shift_id',
        'name',
        'hire_date', // Tambahkan ini
        'pendidikan_terakhir', // Tambahkan ini
        'nomor_telepon', // Tambahkan ini
        'tanggal_lahir', // Tambahkan ini
        'email',
        'nomor_telepon',
    ];

    protected $casts = [
        'hire_date' => 'date', // Ini mungkin sudah ada
        'tanggal_lahir' => 'date', // <--- TAMBAHKAN BARIS INI
    ];

    /**
     * Get the user that owns the employee.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

        public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
