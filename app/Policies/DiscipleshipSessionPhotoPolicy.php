<?php

namespace App\Policies;

use App\Models\DiscipleshipSession;
use App\Models\DiscipleshipSessionPhoto;
use App\Models\User;

class DiscipleshipSessionPhotoPolicy
{
    public function create(User $user, DiscipleshipSession $session): bool
    {
        return $user->isAdmin() || $session->relationship->isParticipant($user);
    }

    public function delete(User $user, DiscipleshipSessionPhoto $photo): bool
    {
        return $user->isAdmin() || $photo->uploaded_by === $user->id;
    }
}
