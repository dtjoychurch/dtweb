<?php

namespace Database\Factories;

use App\Models\DiscipleshipRelationship;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DiscipleshipSession>
 */
class DiscipleshipSessionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'relationship_id' => DiscipleshipRelationship::factory(),
            'created_by' => fn (array $attributes) => DiscipleshipRelationship::find($attributes['relationship_id'])->mentor_id,
            'session_date' => fake()->dateTimeBetween('-3 months', 'now'),
            'title' => fake()->optional()->sentence(4),
            'content' => fake()->paragraphs(3, true),
        ];
    }
}
