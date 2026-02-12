<?php

namespace Tests\Unit;

use App\Models\Dev;
use App\Models\Project;
use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DevModelTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_dev_can_be_created_with_required_attributes()
    {
        $game = Game::factory()->create();
        $dev = Dev::factory()->create([
            'name' => 'John Developer',
            'exp' => 5,
            'stipendio' => 2500,
            'game_id' => $game->id,
        ]);

        $this->assertDatabaseHas('devs', [
            'id' => $dev->id,
            'name' => 'John Developer',
            'exp' => 5,
            'stipendio' => 2500,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_dev_belongs_to_a_project()
    {
        $game = Game::factory()->create();
        $project = Project::factory()->create([
            'game_id' => $game->id,
        ]);
        $dev = Dev::factory()->create([
            'project_id' => $project->id,
            'game_id' => $game->id,
        ]);

        $this->assertInstanceOf(Project::class, $dev->project);
        $this->assertEquals($project->id, $dev->project->id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_dev_can_be_without_project_assignment()
    {
        $game = Game::factory()->create();
        $dev = Dev::factory()->create([
            'project_id' => null,
            'game_id' => $game->id,
        ]);

        $this->assertNull($dev->project_id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_dev_belongs_to_a_game()
    {
        $game = Game::factory()->create();
        $dev = Dev::factory()->create([
            'game_id' => $game->id,
        ]);

        $this->assertEquals($game->id, $dev->game_id);
    }
}
