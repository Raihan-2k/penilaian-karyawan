<?php

use Illuminate\Database\Migrations\Migration; // Koreksi: use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema; // Koreksi: use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade'); // Tugas yang dikumpulkan
            $table->foreignId('submitted_by_employee_id')->constrained('employees')->onDelete('cascade'); // Karyawan yang mengumpulkan
            $table->string('submission_file_path'); // Path file yang disimpan (contoh: storage/app/submissions/tugas_abc.pdf)
            $table->text('comments')->nullable(); // Komentar tambahan dari karyawan
            $table->timestamp('submission_date'); // Tanggal dan waktu pengumpulan
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_submissions');
    }
};
