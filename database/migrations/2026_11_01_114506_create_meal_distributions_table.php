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
        Schema::create('meal_distributions', function (Blueprint $table) {
            $table->id('id_distribution');
            $table->foreignId('id_school')->constrained('schools', 'id_school')->onDelete('restrict');
            $table->date('distribution_date');
            $table->string('menu_description');
            $table->unsignedInteger('portion_count');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_distributions');
    }
};
