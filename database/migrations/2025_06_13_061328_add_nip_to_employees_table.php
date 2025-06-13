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
            // Tambahkan kolom NIP, pastikan unik dan setelah ID, tidak nullable
            $table->string('nip')->unique()->after('id');
            // Hapus kolom email jika Anda tidak memerlukannya sama sekali di database
            // Jika ingin tetap ada tapi tidak di form, jangan hapus baris ini
            // $table->dropColumn('email'); // Hapus baris ini jika Anda tidak ingin menghapus kolom 'email' dari database
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Saat rollback, hapus kolom NIP
            $table->dropColumn('nip');
            // Tambahkan kembali kolom email jika Anda menghapusnya di up()
            // $table->string('email')->unique()->nullable()->after('name'); // Tambahkan kembali jika dihapus
        });
    }
};