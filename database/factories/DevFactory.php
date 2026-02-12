<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dev>
 */
class DevFactory extends Factory
{    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'project_id' =>null,
            'exp' => fake()->numberBetween(1, 10),
            'stipendio' => fake()->numberBetween(1000, 3000),
            'game_id' => null,
        ];
    }
}