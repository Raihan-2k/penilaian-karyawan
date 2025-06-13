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
        Schema::create('employee_logins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('nip')->unique(); // NIP sebagai username login absensi
            $table->string('password');
            $table->timestamp('last_login_at')->nullable();
            $table->boolean('must_change_password')->default(true); // Untuk login pertama kali
            $table->rememberToken(); // Untuk fitur "remember me"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_logins');
    }
};