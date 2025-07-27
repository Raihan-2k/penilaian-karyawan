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
        Schema::create('sales_proofs', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Judul bukti penjualan (misal: Bukti Penjualan Juli)
            $table->text('description')->nullable(); // Deskripsi atau catatan
            $table->foreignId('uploaded_by_employee_id')->constrained('employees')->onDelete('cascade'); // Administrator yang mengunggah
            $table->string('file_path'); // Path file bukti penjualan (misal: storage/app/sales_proofs/bukti_juli.pdf)
            $table->enum('status', ['pending', 'validated', 'rejected'])->default('pending'); // Status validasi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_proofs');
    }
};
