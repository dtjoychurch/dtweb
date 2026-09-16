<?php

namespace Database\Factories;

use App\Models\DiscipleshipRelationship;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DiscipleshipGoal>
 */
class DiscipleshipGoalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'relationship_id' => DiscipleshipRelationship::factory(),
            'created_by' => fn (array $attributes) => DiscipleshipRelationship::find($attributes['relationship_id'])->mentor_id,
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'status' => 'in_progress',
            'started_at' => fake()->dateTimeBetween('-2 months', 'now'),
            'completed_at' => null,
            'visibility' => 'shared',
        ];
    }
}
