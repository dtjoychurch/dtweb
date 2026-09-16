<?php

namespace Database\Factories;

use App\Models\DiscipleshipSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DiscipleshipComment>
 */
class DiscipleshipCommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'session_id' => DiscipleshipSession::factory(),
            'user_id' => User::factory(),
            'content' => fake()->sentence(10),
        ];
    }
}
