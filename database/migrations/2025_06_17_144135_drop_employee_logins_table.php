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
        Schema::dropIfExists('employee_logins'); // Menghapus tabel employee_logins
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Saat rollback, Anda perlu membuat ulang tabel employee_logins
        // Ini akan diperlukan jika Anda ingin mengembalikan perubahan ini di masa depan
        Schema::create('employee_logins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('nip')->unique();
            $table->string('password');
            $table->timestamp('last_login_at')->nullable();
            $table->boolean('must_change_password')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }
};