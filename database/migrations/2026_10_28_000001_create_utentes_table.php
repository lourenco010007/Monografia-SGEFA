<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('utentes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('utilizador_id')
                ->nullable()
                ->unique()
                ->constrained('utilizadores')
                ->nullOnDelete();
            $table->string('nome');
            $table->string('telefone', 50);
            $table->string('bairro');
            $table->text('morada');
            $table->string('estado')->default('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utentes');
    }
};
