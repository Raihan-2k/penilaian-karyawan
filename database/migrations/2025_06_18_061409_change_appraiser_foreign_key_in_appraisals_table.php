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
        Schema::table('appraisals', function (Blueprint $table) {
            // 1. Drop foreign key constraint yang lama (yang merujuk ke tabel 'users')
            // Pastikan nama constraint ini benar (sesuai error sebelumnya: appraisals_appraiser_id_foreign)
            $table->dropForeign(['appraiser_id']); // Menghapus foreign key constraint yang salah

            // 2. Tambahkan foreign key constraint yang baru (merujuk ke tabel 'employees')
            // Kolom 'appraiser_id' sudah ada dan tipenya sudah unsignedBigInteger (dari foreignId() sebelumnya)
            // Jadi, kita hanya perlu menambahkan constraint baru.
            // Tidak perlu $table->unsignedBigInteger('appraiser_id')->change(); lagi
            $table->foreign('appraiser_id')->references('id')->on('employees')->onDelete('cascade'); // Menambahkan foreign key baru
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appraisals', function (Blueprint $table) {
            // Saat rollback, drop foreign key ke 'employees'
            $table->dropForeign(['appraiser_id']);

            // Tambahkan kembali foreign key yang lama ke 'users'
            // CATATAN PENTING: Ini bisa menyebabkan masalah jika tabel `users` sudah tidak ada atau kosong.
            // Untuk prototipe, ini sering diabaikan atau disarankan `migrate:fresh` jika perlu rollback history.
            $table->foreign('appraiser_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};