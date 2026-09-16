<?php

namespace App\Policies;

use App\Models\DiscipleshipComment;
use App\Models\DiscipleshipSession;
use App\Models\User;

class DiscipleshipCommentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, DiscipleshipComment $comment): bool
    {
        return $user->isAdmin() || $comment->session->relationship->isParticipant($user);
    }

    public function create(User $user, DiscipleshipSession $session): bool
    {
        return $user->isAdmin() || $session->relationship->isParticipant($user);
    }

    public function update(User $user, DiscipleshipComment $comment): bool
    {
        return $comment->user_id === $user->id;
    }

    public function delete(User $user, DiscipleshipComment $comment): bool
    {
        return $user->isAdmin() || $comment->user_id === $user->id;
    }
}
