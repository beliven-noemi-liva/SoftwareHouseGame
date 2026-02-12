<?php

namespace Tests\Unit;

use App\Models\Sale;
use App\Models\Project;
use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleModelTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_sale_can_be_created_with_required_attributes()
    {
        $sale = Sale::factory()->create([
            'name' => 'John Sales',
            'exp' => 3,
            'stipendio' => 2000,
        ]);

        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'name' => 'John Sales',
            'exp' => 3,
            'stipendio' => 2000,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_sale_has_many_projects()
    {
        $game = Game::factory()->create();
        $sale = Sale::factory()->create([
            'game_id' => $game->id,
        ]);
        $projects = Project::factory()->count(4)->create([
            'sale_id' => $sale->id,
            'game_id' => $game->id,
        ]);

        $this->assertCount(4, $sale->projects);
        $this->assertTrue($sale->projects->contains($projects[0]));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_sale_has_one_current_project_with_ready_status()
    {
        $game = Game::factory()->create();
        $sale = Sale::factory()->create([
            'game_id' => $game->id,
        ]);

        // Creare progetti con stati diversi
        Project::factory()->create([
            'sale_id' => $sale->id,
            'game_id' => $game->id,
            'status' => 'in_progress',
        ]);

        $readyProject = Project::factory()->create([
            'sale_id' => $sale->id,
            'game_id' => $game->id,
            'status' => 'ready',
        ]);

        Project::factory()->create([
            'sale_id' => $sale->id,
            'game_id' => $game->id,
            'status' => 'complete',
        ]);

        $currentProject = $sale->currentProject;

        $this->assertEquals($readyProject->id, $currentProject->id);
        $this->assertEquals('ready', $currentProject->status);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function procaccia_progetto_creates_a_new_project()
    {
        $game = Game::factory()->create();
        $sale = Sale::factory()->create([
            'game_id' => $game->id,
            'exp' => 5,
        ]);

        $project = $sale->procacciaProgetto($game);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'sale_id' => $sale->id,
            'game_id' => $game->id,
            'status' => 'ready',
        ]);
    }

}
