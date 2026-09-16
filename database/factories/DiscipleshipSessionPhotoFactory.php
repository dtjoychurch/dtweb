<?php

namespace Database\Factories;

use App\Models\DiscipleshipSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DiscipleshipSessionPhoto>
 */
class DiscipleshipSessionPhotoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'session_id' => DiscipleshipSession::factory(),
            'uploaded_by' => User::factory(),
            'path' => 'uploads/session-photos/1/'.fake()->uuid().'.jpg',
        ];
    }
}
