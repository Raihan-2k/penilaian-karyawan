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
        Schema::create('appraisal_criterion_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appraisal_id')->constrained()->onDelete('cascade'); // foreign key ke tabel appraisals
            $table->foreignId('appraisal_criterion_id')->constrained('appraisal_criteria')->onDelete('cascade');
            $table->integer('score');
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appraisal_criterion_scores');
    }
};