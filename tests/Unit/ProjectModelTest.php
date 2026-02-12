<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\Game;
use App\Models\Sale;
use App\Models\Dev;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectModelTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_project_can_be_created_with_required_attributes()
    {
        $game = Game::factory()->create();
        $sale = Sale::factory()->create([
            'game_id' => $game->id,
        ]);

        $project = Project::factory()->create([
            'name' => 'Test Project',
            'complex' => 5,
            'value' => 5000,
            'game_id' => $game->id,
            'sale_id' => $sale->id,
            'status' => 'ready',
        ]);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Test Project',
            'complex' => 5,
            'value' => 5000,
            'status' => 'ready',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_project_has_many_devs()
    {
        $game = Game::factory()->create();
        $sale = Sale::factory()->create([
            'game_id' => $game->id,
        ]);
        $project = Project::factory()->create([
            'game_id' => $game->id,
            'sale_id' => $sale->id,
        ]);

        $devs = Dev::factory()->count(3)->create([
            'project_id' => $project->id,
            'game_id' => $game->id,
        ]);

        $this->assertCount(3, $project->devs);
        $this->assertTrue($project->devs->contains($devs[0]));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_project_belongs_to_a_sale()
    {
        $game = Game::factory()->create();
        $sale = Sale::factory()->create([
            'game_id' => $game->id,
        ]);
        $project = Project::factory()->create([
            'sale_id' => $sale->id,
            'game_id' => $game->id,
        ]);

        $this->assertInstanceOf(Sale::class, $project->sale);
        $this->assertEquals($sale->id, $project->sale->id);
    }

   #[\PHPUnit\Framework\Attributes\Test]
    public function a_project_belongs_to_a_game()
    {
        $game = Game::factory()->create();
        $sale = Sale::factory()->create([
            'game_id' => $game->id,
        ]);
        $project = Project::factory()->create([
            'sale_id' => $sale->id,
            'game_id' => $game->id,
        ]);

        $this->assertInstanceOf(Game::class, $project->game);
        $this->assertEquals($game->id, $project->game->id);
    }


    #[\PHPUnit\Framework\Attributes\Test]
    public function a_project_can_have_different_statuses()
    {
        $game = Game::factory()->create();
        $sale = Sale::factory()->create([
            'game_id' => $game->id,
        ]);

        $readyProject = Project::factory()->create([
            'game_id' => $game->id,
            'sale_id' => $sale->id,
            'status' => 'ready',
        ]);

        $this->assertEquals('ready', $readyProject->status);
    }
}
