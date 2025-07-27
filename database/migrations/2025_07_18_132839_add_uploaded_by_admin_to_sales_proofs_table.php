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
        Schema::table('sales_proofs', function (Blueprint $table) {
            $table->foreignId('uploaded_by_admin_employee_id')->nullable()->after
            ('uploaded_by_employee_id')->constrained('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_proofs', function (Blueprint $table) {
        $table->dropForeign(['uploaded_by_admin_employee_id']);
        $table->dropColumn('uploaded_by_admin_employee_id');
        });
    }
};
