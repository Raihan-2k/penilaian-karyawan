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
        Schema::table('employees', function (Blueprint $table) {
            // Tambahkan kolom password, remember_token, dan email_verified_at
            // Jika kolom 'nip' sudah ada (seperti kasus Anda), tambahkan setelahnya
            // Jika belum ada, maka harus ada di migrasi create_employees_table

            // Pastikan kolom-kolom ini belum ada sebelum menambahkannya
            // Agar tidak terjadi "Duplicate column name"
            if (!Schema::hasColumn('employees', 'password')) {
                $table->string('password')->after('nip');
            }
            if (!Schema::hasColumn('employees', 'remember_token')) {
                $table->rememberToken()->after('password');
            }
            if (!Schema::hasColumn('employees', 'email_verified_at')) {
                // Posisi email_verified_at bisa setelah 'email' jika email ada sebagai kolom terpisah.
                $table->timestamp('email_verified_at')->nullable()->after('name'); // Sesuaikan posisi jika 'email' ada di tempat lain
            }
            if (!Schema::hasColumn('employees', 'must_change_password')) {
                $table->boolean('must_change_password')->default(false)->after('email_verified_at');
            }

            // Tambahkan kolom role
            if (!Schema::hasColumn('employees', 'role')) {
                $table->string('role')->default('karyawan')->after('position');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Saat rollback, hapus kolom-kolom yang ditambahkan
            if (Schema::hasColumn('employees', 'password')) {
                $table->dropColumn('password');
            }
            if (Schema::hasColumn('employees', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
            if (Schema::hasColumn('employees', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
            if (Schema::hasColumn('employees', 'must_change_password')) {
                $table->dropColumn('must_change_password');
            }
            if (Schema::hasColumn('employees', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};