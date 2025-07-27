<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint; // Koreksi: use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema; // Koreksi: use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Ubah tipe kolom 'role' untuk menambahkan 'administrator' dan mengatur urutan
            // Urutan baru: ['admin', 'manager', 'administrator', 'karyawan']
            $table->enum('role', ['admin', 'manager', 'administrator', 'karyawan'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Revert tipe kolom 'role' jika rollback
            // Perhatian: Jika ada data dengan role 'administrator', rollback ini akan gagal.
            // Anda mungkin perlu menghapus/mengubah role 'administrator' secara manual sebelum rollback.
            $table->enum('role', ['manager', 'admin', 'karyawan'])->change(); // Revert ke urutan sebelumnya jika perlu
        });
    }
};
