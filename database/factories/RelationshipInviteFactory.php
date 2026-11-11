<?php

namespace Database\Factories;

use App\Models\RelationshipInvite;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RelationshipInvite>
 */
class RelationshipInviteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => RelationshipInvite::generateUniqueCode(),
            'created_by' => User::factory(),
            'role' => 'mentor',
            'status' => 'pending',
            'expires_at' => RelationshipInvite::defaultExpiry(),
        ];
    }
}
