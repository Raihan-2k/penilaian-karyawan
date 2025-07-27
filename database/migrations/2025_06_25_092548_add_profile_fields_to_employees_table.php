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
            // Tambahkan hanya kolom-kolom yang belum ada di create_employees_table
            // Kolom nip, position, hire_date, password, role, remember_token, email_verified_at, must_change_password
            // seharusnya sudah ada dari migrasi add_auth_fields_to_employees_table.php atau create_employees_table.php

            if (!Schema::hasColumn('employees', 'email')) {
                $table->string('email')->nullable()->after('name'); // Tambahkan setelah 'name'
            }
            if (!Schema::hasColumn('employees', 'pendidikan_terakhir')) {
                $table->string('pendidikan_terakhir')->nullable()->after('email'); // Tambahkan setelah 'email'
            }
            if (!Schema::hasColumn('employees', 'nomor_telepon')) {
                $table->string('nomor_telepon')->nullable()->after('pendidikan_terakhir'); // Tambahkan setelah 'pendidikan_terakhir'
            }
            if (!Schema::hasColumn('employees', 'tanggal_lahir')) {
                $table->date('tanggal_lahir')->nullable()->after('nomor_telepon'); // Tambahkan setelah 'nomor_telepon'
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Saat rollback, hapus kolom-kolom yang ditambahkan di up()
            if (Schema::hasColumn('employees', 'email')) {
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('employees', 'pendidikan_terakhir')) {
                $table->dropColumn('pendidikan_terakhir');
            }
            if (Schema::hasColumn('employees', 'nomor_telepon')) {
                $table->dropColumn('nomor_telepon');
            }
            if (Schema::hasColumn('employees', 'tanggal_lahir')) {
                $table->dropColumn('tanggal_lahir');
            }
        });
    }
};