<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('users', 'utilizadores');

        Schema::table('utilizadores', function (Blueprint $table): void {
            $table->renameColumn('name', 'nome');
            $table->dropColumn(['email_verified_at', 'remember_token', 'created_at', 'updated_at']);
            $table->string('perfil')->default('utente');
            $table->boolean('activo')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('utilizadores', function (Blueprint $table): void {
            $table->dropColumn(['perfil', 'activo']);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->renameColumn('nome', 'name');
        });

        Schema::rename('utilizadores', 'users');
    }
};
