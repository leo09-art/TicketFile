<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->integer('ticket_number');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('login')->onDelete('set null');
            $table->foreignId('counter_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['en_attente', 'appele', 'traite', 'absent', 'annule'])->default('en_attente');
            $table->timestamp('called_at')->nullable();
            $table->timestamp('treated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};