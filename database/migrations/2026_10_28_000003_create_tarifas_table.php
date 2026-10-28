<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarifas', function (Blueprint $table): void {
            $table->id();
            $table->string('descricao');
            $table->decimal('preco_m3', 10, 2);
            $table->decimal('taxa_multa_pct', 5, 2);
            $table->unsignedSmallInteger('prazo_dias');
            $table->date('vigente_desde')->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarifas');
    }
};
