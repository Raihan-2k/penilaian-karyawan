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
        Schema::table('employees', function (Blueprint $table) {
            // Tambahkan kolom user_id sebagai foreign key
            // nullable() sementara agar bisa diisi setelah migrasi data
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('cascade');

            // Hapus kolom-kolom yang akan dipindahkan ke tabel users
            $table->dropColumn(['email', 'password']);

            // Jika kolom 'role' sudah ada di tabel employees, hapus juga
            if (Schema::hasColumn('employees', 'role')) {
                $table->dropColumn('role');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Tambahkan kembali kolom-kolom yang dihapus jika migrasi di-rollback
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('karyawan'); // Sesuaikan default role jika perlu

            // Hapus foreign key dan kolom user_id
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};