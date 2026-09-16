<?php

namespace App\Policies;

use App\Models\DiscipleshipRecord;
use App\Models\DiscipleshipRelationship;
use App\Models\User;

class DiscipleshipRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, DiscipleshipRecord $record): bool
    {
        return $user->isAdmin() || $record->isVisibleTo($user);
    }

    public function create(User $user, DiscipleshipRelationship $relationship): bool
    {
        return $user->isAdmin() || $relationship->isParticipant($user);
    }

    public function update(User $user, DiscipleshipRecord $record): bool
    {
        return $user->isAdmin() || $record->created_by === $user->id;
    }

    public function delete(User $user, DiscipleshipRecord $record): bool
    {
        return $user->isAdmin() || $record->created_by === $user->id;
    }
}
