<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->streetName(),
            'start_day_available' => 'Monday',
            'end_day_available' => 'Friday',
            'min_time_available' => 15,
            'max_time_available' => 60,
            'created_by' => 1,
            'created_at' => now(),
        ];
    }
}
