<?php

namespace App\Policies;

use App\Models\Tarifa;
use App\Models\User;

class TarifaPolicy
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
        return false;
    }

    public function view(User $user, Tarifa $tarifa): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Tarifa $tarifa): bool
    {
        return false;
    }

    public function delete(User $user, Tarifa $tarifa): bool
    {
        return false;
    }
}
