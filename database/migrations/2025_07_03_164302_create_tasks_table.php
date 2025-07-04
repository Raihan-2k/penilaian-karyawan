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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Judul tugas
            $table->text('description')->nullable(); // Deskripsi tugas
            $table->foreignId('assigned_to_employee_id')->constrained('employees')->onDelete('cascade'); // Karyawan yang diberi tugas
            $table->foreignId('assigned_by_manager_id')->constrained('employees')->onDelete('cascade'); // Manager yang memberi tugas
            $table->date('deadline')->nullable(); // Batas waktu pengumpulan
            $table->enum('status', ['pending', 'in_progress', 'completed', 'submitted'])->default('pending'); // Status tugas
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
