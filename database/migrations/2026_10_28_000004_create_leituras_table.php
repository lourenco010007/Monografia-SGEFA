<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leituras', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('contador_id')->constrained('contadores')->restrictOnDelete();
            $table->foreignId('agente_id')->constrained('utilizadores')->restrictOnDelete();
            $table->decimal('leitura_anterior', 12, 3);
            $table->decimal('leitura_actual', 12, 3);
            $table->decimal('consumo_m3', 12, 3);
            $table->date('data_leitura');
            $table->index(['contador_id', 'data_leitura']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leituras');
    }
};
