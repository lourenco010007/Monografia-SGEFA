<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
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

    public function view(User $user, User $subject): bool
    {
        return $user->is($subject);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, User $subject): bool
    {
        return $user->is($subject);
    }

    public function delete(User $user, User $subject): bool
    {
        return false;
    }
}
