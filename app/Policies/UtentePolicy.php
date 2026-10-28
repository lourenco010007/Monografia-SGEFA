<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Utente;

class UtentePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin() && in_array($ability, ['viewAny', 'view', 'create', 'update'], true)) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, Utente $utente): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Utente $utente): bool
    {
        return false;
    }

    public function delete(User $user, Utente $utente): bool
    {
        return false;
    }
}
