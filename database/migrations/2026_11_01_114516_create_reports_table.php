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
        Schema::create('reports', function (Blueprint $table) {
            $table->id('id_report');
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('restrict');
            $table->foreignId('id_school')->constrained('schools', 'id_school')->onDelete('restrict');
            $table->foreignId('id_distribution')->constrained('meal_distributions', 'id_distribution')->onDelete('restrict');
            $table->dateTime('incident_time');
            $table->string('title');
            $table->text('description');
            $table->unsignedInteger('affected_count')->nullable();
            $table->string('status', 20)->index();
            $table->string('evidence_path')->nullable();
            $table->string('evidence_original_name')->nullable();
            $table->string('evidence_mime')->nullable();
            $table->timestamps();
            $table->index(['status', 'id_school']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
