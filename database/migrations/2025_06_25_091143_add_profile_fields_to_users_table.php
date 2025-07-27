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
        Schema::table('users', function (Blueprint $table) {
        $table->string('nip')->nullable()->after('name');
        $table->string('pendidikan_terakhir')->nullable()->after('nip');
        $table->string('nomor_telepon')->nullable()->after('pendidikan_terakhir');
        $table->date('tanggal_lahir')->nullable()->after('nomor_telepon');
        $table->string('position')->nullable()->after('tanggal_lahir');
        $table->date('hire_date')->nullable()->after('position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
            'nip',
            'pendidikan_terakhir',
            'nomor_telepon',
            'tanggal_lahir',
            'position',
            'hire_date',
            ]);
        });
    }
};
