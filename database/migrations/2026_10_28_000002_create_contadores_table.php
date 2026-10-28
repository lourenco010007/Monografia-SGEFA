<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contadores', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('utente_id')->constrained('utentes')->restrictOnDelete();
            $table->string('numero_serie')->unique();
            $table->string('localizacao');
            $table->date('data_instalacao');
            $table->string('estado')->default('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contadores');
    }
};
