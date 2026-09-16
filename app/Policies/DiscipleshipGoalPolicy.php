<?php

namespace App\Policies;

use App\Models\DiscipleshipGoal;
use App\Models\DiscipleshipRelationship;
use App\Models\User;

class DiscipleshipGoalPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, DiscipleshipGoal $goal): bool
    {
        return $user->isAdmin() || $goal->isVisibleTo($user);
    }

    public function create(User $user, DiscipleshipRelationship $relationship): bool
    {
        return $user->isAdmin() || $relationship->isParticipant($user);
    }

    public function update(User $user, DiscipleshipGoal $goal): bool
    {
        return $user->isAdmin() || $goal->created_by === $user->id;
    }

    public function delete(User $user, DiscipleshipGoal $goal): bool
    {
        return $user->isAdmin() || $goal->created_by === $user->id;
    }
}
