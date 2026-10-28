<?php

namespace App\Policies;

use App\Models\Leitura;
use App\Models\User;

class LeituraPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin() && in_array($ability, ['viewAny', 'view', 'create'], true)) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->perfil === User::PROFILE_AGENT;
    }

    public function view(User $user, Leitura $leitura): bool
    {
        return $user->perfil === User::PROFILE_AGENT;
    }

    public function create(User $user): bool
    {
        return $user->perfil === User::PROFILE_AGENT;
    }

    public function update(User $user, Leitura $leitura): bool
    {
        return false;
    }

    public function delete(User $user, Leitura $leitura): bool
    {
        return false;
    }
}
