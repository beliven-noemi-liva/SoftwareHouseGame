<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'value' => fake()->numberBetween(5000, 50000),
            'game_id' => Game::factory(),
            'status' => 'ready',
            'complex' => fake()->numberBetween(30,50),
            'sale_id' => Sale::factory(),
            'initial_complex' => null,
        ];
    }
}