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
        Schema::table('attendances', function (Blueprint $table) {
            // Ubah kolom check_in_time dan check_out_time agar bisa NULL
            $table->time('check_in_time')->nullable()->change();
            $table->time('check_out_time')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Saat rollback, kembalikan kolom menjadi NOT NULL
            // Ini akan gagal jika ada nilai NULL di kolom tersebut.
            // Untuk prototipe, ini bisa diabaikan atau disarankan migrate:fresh jika perlu rollback history.
            $table->time('check_in_time')->nullable(false)->change();
            $table->time('check_out_time')->nullable(false)->change();
        });
    }
};