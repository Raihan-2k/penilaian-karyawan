<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; // <-- Tetap diimpor, tapi Employee akan extends Authenticatable
use Illuminate\Foundation\Auth\User as Authenticatable; // <-- PENTING: Impor ini

// Model Employee sekarang extend Authenticatable untuk fitur autentikasi
class Employee extends Authenticatable // <-- UBAH INI (sebelumnya extends Model)
{
    use HasFactory;

    // Properti $fillable mendefinisikan kolom-kolom yang boleh diisi secara massal (mass assignable).
    // Ini termasuk kolom-kolom autentikasi yang baru ditambahkan di migrasi.
    protected $fillable = [
        'nip', // Nomor Induk Pegawai
        'name', // Nama Karyawan
        'email', // Alamat email karyawan (tetap ada di DB, tapi tidak dipakai untuk login utama)
        'password', // Hash password untuk login
        'remember_token', // Token untuk fitur "remember me" (dikelola Laravel)
        'email_verified_at', // Timestamp verifikasi email (opsional)
        'must_change_password', // Flag untuk memaksa ganti password pada login pertama
        'pendidikan_terakhir', // Tambahkan ini
        'nomor_telepon', // Tambahkan ini
        'tanggal_lahir', // Tambahkan ini
        'position', // Jabatan/Posisi karyawan
        'role', // Peran karyawan: 'manager' atau 'karyawan'
        'hire_date', // Tanggal mulai bekerja
    ];

    // Properti $hidden mendefinisikan kolom-kolom yang harus disembunyikan
    // ketika model di-serialized menjadi array atau JSON (misal untuk API).
    // Password dan remember_token sangat penting untuk disembunyikan.
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Properti $casts mendefinisikan casting tipe data kolom.
    // Laravel akan otomatis mengkonversi nilai kolom ini ke tipe yang ditentukan
    // saat diambil dari database.
    protected $casts = [
        'hire_date' => 'date', // Konversi ke objek Carbon Date
        'email_verified_at' => 'datetime', // Konversi ke objek Carbon DateTime
        'must_change_password' => 'boolean', // Konversi ke tipe boolean
        'tanggal_lahir' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relasi (Relationships)
    |--------------------------------------------------------------------------
    | Definisi relasi antar model untuk memudahkan pengambilan data terkait.
    */

    // Relasi One-to-Many: Satu Karyawan bisa memiliki banyak Penilaian (Appraisal)
    public function appraisals()
    {
        return $this->hasMany(Appraisal::class);
    }

    // Relasi One-to-Many: Satu Karyawan bisa memiliki banyak Record Absensi (Attendance)
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Catatan:
    // Relasi 'loginAccount' yang sebelumnya ada di Employee untuk EmployeeLogin
    // tidak lagi diperlukan karena Model Employee sendiri sekarang adalah user yang bisa login.
}