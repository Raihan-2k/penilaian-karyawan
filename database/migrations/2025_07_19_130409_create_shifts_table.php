<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Nama shift (contoh: Shift Pagi, Shift Malam)
            $table->text('description')->nullable();
            // Jadwal kerja: simpan sebagai JSON array of integers (0=Minggu, 1=Senin, ..., 6=Sabtu)
            // Contoh: {"working_days": [1,2,3,4,5,6], "off_days": [0]} untuk 6 hari kerja + Minggu libur
            $table->json('schedule');
            $table->time('start_time')->nullable(); // Waktu mulai kerja normal untuk shift ini
            $table->time('end_time')->nullable();   // Waktu selesai kerja normal untuk shift ini
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shifts');
    }
};
