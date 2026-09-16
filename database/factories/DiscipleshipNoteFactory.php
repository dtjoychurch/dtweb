<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DiscipleshipNote>
 */
class DiscipleshipNoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'session_id' => null,
            'user_id' => User::factory(),
            'title' => fake()->optional()->sentence(4),
            'content' => fake()->paragraph(),
        ];
    }
}
