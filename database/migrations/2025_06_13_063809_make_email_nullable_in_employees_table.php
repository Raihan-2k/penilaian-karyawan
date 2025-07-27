<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Ubah kolom email agar bisa NULL
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Saat rollback, kembalikan kolom email agar tidak bisa NULL
            // CATATAN: Ini hanya akan berhasil jika tidak ada baris yang memiliki nilai NULL di kolom email
            // Jika ada, Anda perlu mengisi nilai default terlebih dahulu sebelum rollback.
            $table->string('email')->nullable(false)->change();
        });
    }
};