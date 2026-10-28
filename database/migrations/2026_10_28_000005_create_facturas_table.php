<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('leitura_id')->unique()->constrained('leituras')->restrictOnDelete();
            $table->foreignId('utente_id')->constrained('utentes')->restrictOnDelete();
            $table->foreignId('tarifa_id')->constrained('tarifas')->restrictOnDelete();
            $table->string('numero')->unique();
            $table->decimal('valor_total', 12, 2);
            $table->date('data_emissao');
            $table->date('data_vencimento');
            $table->string('estado')->default('emitida');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
