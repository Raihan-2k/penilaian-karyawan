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
            $table->dropColumn('overtime_hours'); // Menghapus kolom overtime_hours
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Saat rollback, tambahkan kembali kolom overtime_hours
            $table->decimal('overtime_hours', 5, 2)->default(0)->after('check_out_time');
        });
    }
};