<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Game;
use App\Models\Dev;
use App\Models\Sale;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_have_multiple_games_with_related_data()
    {
        $user = User::factory()->create();

        $game1 = Game::factory()->create(['user_id' => $user->id]);
        $game2 = Game::factory()->create(['user_id' => $user->id]);

        $sale1 = Sale::factory()->create(['game_id' => $game1->id]);
        $dev1 = Dev::factory()->create(['game_id' => $game1->id]);

        $sale2 = Sale::factory()->create(['game_id' => $game2->id]);
        $dev2 = Dev::factory()->create(['game_id' => $game2->id]);

        $this->assertCount(2, $user->games);
        $this->assertCount(1, $game1->sales);
        $this->assertCount(1, $game1->devs);
        $this->assertCount(1, $game2->sales);
        $this->assertCount(1, $game2->devs);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_project_can_be_associated_with_multiple_devs_and_one_sale()
    {
        $game = Game::factory()->create();
        $sale = Sale::factory()->create(['game_id' => $game->id]);
        $project = Project::factory()->create(['sale_id' => $sale->id, 'game_id' => $game->id]);

        $devs = Dev::factory()->count(3)->create(['project_id' => $project->id, 'game_id' => $game->id]);

        $this->assertCount(3, $project->devs);
        $this->assertEquals($sale->id, $project->sale->id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_sale_can_have_multiple_projects()
    {
        $game = Game::factory()->create();
        $sale = Sale::factory()->create(['game_id' => $game->id]);

        $projects = Project::factory()->count(5)->create(['sale_id' => $sale->id, 'game_id' => $game->id]);

        $this->assertCount(5, $sale->projects);
    }

}
