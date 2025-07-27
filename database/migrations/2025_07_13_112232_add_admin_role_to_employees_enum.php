<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Ubah tipe kolom 'role' untuk menambahkan 'administrator'
            // Ini akan mengubah enum dari ['manager', 'admin', 'karyawan'] menjadi ['manager', 'admin', 'administrator', 'karyawan']
            $table->enum('role', ['manager', 'admin', 'administrator', 'karyawan'])->change();
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
            $table->enum('role', ['manager', 'admin', 'karyawan'])->change();
        });
    }
};
