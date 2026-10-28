<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'nome' => 'Administrador de demonstração',
                'password' => 'password',
                'perfil' => User::PROFILE_ADMIN,
                'activo' => true,
            ],
        );
    }
}
