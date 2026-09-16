<?php

namespace App\Policies;

use App\Models\DiscipleshipRelationship;
use App\Models\User;

class DiscipleshipRelationshipPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, DiscipleshipRelationship $relationship): bool
    {
        return $user->isAdmin() || $relationship->isParticipant($user);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, DiscipleshipRelationship $relationship): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, DiscipleshipRelationship $relationship): bool
    {
        return $user->isAdmin();
    }
}
