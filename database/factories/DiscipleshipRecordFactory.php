<?php

namespace Database\Factories;

use App\Models\DiscipleshipRecordType;
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
            // 遷移檔已經固定灌了 8 種預設類型，測試環境每次都會跑過那個 migration，
            // 直接挑一個既有的最省事；沒有的話（理論上不會發生）才臨時建一個。
            'type_id' => fn () => DiscipleshipRecordType::inRandomOrder()->value('id') ?? DiscipleshipRecordType::factory()->create()->id,
            'title' => fake()->sentence(4),
            'content' => fake()->paragraph(),
            'visibility' => 'shared',
            'occurred_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ];
    }
}
