<?php

namespace App\Policies;

use App\Models\Feedback;
use App\Models\User;

class FeedbackPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Feedback $feedback): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function delete(User $user, Feedback $feedback): bool
    {
        return $user->isAdmin() || $feedback->user_id === $user->id;
    }
}
