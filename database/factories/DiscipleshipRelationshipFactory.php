<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DiscipleshipRelationship>
 */
class DiscipleshipRelationshipFactory extends Factory
{
    public function definition(): array
    {
        return [
            'mentor_id' => User::factory(),
            'disciple_id' => User::factory(),
            'started_at' => fake()->dateTimeBetween('-1 year', '-1 month'),
            'ended_at' => null,
            'status' => 'active',
        ];
    }
}
