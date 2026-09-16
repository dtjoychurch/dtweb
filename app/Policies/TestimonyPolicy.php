<?php

namespace App\Policies;

use App\Models\Testimony;
use App\Models\User;

/**
 * Testimonies are public content on the home page — anyone can view them,
 * but only admins may manage them.
 */
class TestimonyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Testimony $testimony): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Testimony $testimony): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Testimony $testimony): bool
    {
        return $user->isAdmin();
    }
}
