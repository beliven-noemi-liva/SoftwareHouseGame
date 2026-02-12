<?php

namespace Tests\Feature;

use App\Models\Sale;
use App\Models\Game;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function the_method_procaccia_generates_project_with_correct_value()
    {
        $game = Game::factory()->create();
        $sale = Sale::factory()->create([
            'game_id' => $game->id,
            'exp' => 5,
        ]);

        $project = $sale->procacciaProgetto($game);

        $this->assertDatabaseHas('projects', [
            'game_id' => $game->id,
            'sale_id' => $sale->id,
        ]);
        $this->assertEquals('ready', $project->status);
    }


    #[\PHPUnit\Framework\Attributes\Test]
    public function current_project_returns_only_ready_status_projects()
    {
        $game = Game::factory()->create();
        $sale = Sale::factory()->create([
            'game_id' => $game->id,
        ]);

        Project::factory()->create([
            'sale_id' => $sale->id,
            'game_id' => $game->id,
            'status' => 'in_progress',
        ]);

        Project::factory()->create([
            'sale_id' => $sale->id,
            'game_id' => $game->id,
            'status' => 'complete',
        ]);

        $readyProject = Project::factory()->create([
            'sale_id' => $sale->id,
            'game_id' => $game->id,
            'status' => 'ready',
        ]);

        $currentProject = $sale->currentProject;

        $this->assertEquals($readyProject->id, $currentProject->id);
    }
}
