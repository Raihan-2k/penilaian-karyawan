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
        Schema::create('sales_validations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_proof_id')->constrained('sales_proofs')->onDelete('cascade'); // Bukti penjualan yang divalidasi
            $table->foreignId('validated_by_employee_id')->constrained('employees')->onDelete('cascade'); // Manager yang memvalidasi
            $table->enum('status', ['validated', 'rejected']); // Hasil validasi
            $table->text('comments')->nullable(); // Komentar dari Manager
            $table->timestamps();

            $table->unique(['sales_proof_id', 'validated_by_employee_id']); // Satu validasi per bukti per manager
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_validations');
    }
};
