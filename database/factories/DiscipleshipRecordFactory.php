<?php

namespace Database\Factories;

use App\Models\DiscipleshipRecord;
use App\Models\DiscipleshipRelationship;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DiscipleshipRecord>
 */
class DiscipleshipRecordFactory extends Factory
{
    public function definition(): array
    {
        return [
            'relationship_id' => DiscipleshipRelationship::factory(),
            'session_id' => null,
            'created_by' => fn (array $attributes) => DiscipleshipRelationship::find($attributes['relationship_id'])->mentor_id,
            'type' => fake()->randomElement(DiscipleshipRecord::TYPES),
            'title' => fake()->sentence(4),
            'content' => fake()->paragraph(),
            'visibility' => 'shared',
            'occurred_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ];
    }
}
