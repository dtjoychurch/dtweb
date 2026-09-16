<?php

namespace App\Policies;

use App\Models\DiscipleshipSession;
use App\Models\User;

class DiscipleshipSessionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, DiscipleshipSession $session): bool
    {
        return $user->isAdmin() || $session->relationship->isParticipant($user);
    }

    public function create(User $user, \App\Models\DiscipleshipRelationship $relationship): bool
    {
        return $user->isAdmin() || $relationship->isParticipant($user);
    }

    public function update(User $user, DiscipleshipSession $session): bool
    {
        return $user->isAdmin() || $session->created_by === $user->id;
    }

    public function delete(User $user, DiscipleshipSession $session): bool
    {
        return $user->isAdmin() || $session->created_by === $user->id;
    }
}
