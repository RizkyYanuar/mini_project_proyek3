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
        Schema::create('report_forwardings', function (Blueprint $table) {
            $table->id('id_forwarding');
            $table->foreignId('id_report')->constrained('reports', 'id_report');
            $table->foreignId('id_user')->constrained('users', 'id_user');
            $table->string('recipient_type');
            $table->string('recipient_name');
            $table->text('note');
            $table->dateTime('forwarded_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_forwardings');
    }
};
