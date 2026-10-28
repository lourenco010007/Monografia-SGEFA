<?php

namespace App\Policies;

use App\Models\Factura;
use App\Models\User;

class FacturaPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin() && in_array($ability, ['viewAny', 'view'], true)) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return in_array($user->perfil, [User::PROFILE_AGENT, User::PROFILE_TENANT], true);
    }

    public function view(User $user, Factura $factura): bool
    {
        if ($user->perfil === User::PROFILE_AGENT) {
            return true;
        }

        return $user->perfil === User::PROFILE_TENANT
            && $user->utente()->whereKey($factura->utente_id)->exists();
    }
}
