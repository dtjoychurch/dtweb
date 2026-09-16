<?php

namespace App\Policies;

use App\Models\HeroSlide;
use App\Models\User;

/**
 * Hero slides are public content on the home page — anyone can view them
 * (no view/viewAny ability is even checked for that), but only admins may
 * manage them.
 */
class HeroSlidePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, HeroSlide $heroSlide): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, HeroSlide $heroSlide): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, HeroSlide $heroSlide): bool
    {
        return $user->isAdmin();
    }
}
