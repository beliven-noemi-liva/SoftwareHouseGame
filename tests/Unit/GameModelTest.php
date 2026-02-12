<?php

namespace Tests\Unit;

use App\Models\Game;
use App\Models\User;
use App\Models\Dev;
use App\Models\Sale;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameModelTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_game_can_be_created_with_required_attributes()
    {
        $user = User::factory()->create();
        $game = Game::factory()->create([
            'user_id' => $user->id,
            'name' => 'Test Game',
            'state' => 'in_progress',
            'patrimonio' => 5000,
        ]);

        $this->assertDatabaseHas('games', [
            'id' => $game->id,
            'name' => 'Test Game',
            'state' => 'in_progress',
            'patrimonio' => 5000,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_game_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $game = Game::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(User::class, $game->user);
        $this->assertEquals($user->id, $game->user->id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_game_has_many_devs()
    {
        $user = User::factory()->create();
        $game = Game::factory()->create([
            'user_id' => $user->id,
        ]);
        $devs = Dev::factory()->count(5)->create([
            'game_id' => $game->id,
        ]);

        $this->assertCount(5, $game->devs);
        $this->assertTrue($game->devs->contains($devs[0]));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_game_has_many_sales()
    {
        $user = User::factory()->create();
        $game = Game::factory()->create([
            'user_id' => $user->id,
        ]);
        $sales = Sale::factory()->count(3)->create([
            'game_id' => $game->id,
        ]);

        $this->assertCount(3, $game->sales);
        $this->assertTrue($game->sales->contains($sales[0]));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_game_has_many_projects()
    {
        $user = User::factory()->create();
        $game = Game::factory()->create([
            'user_id' => $user->id,
        ]);
        $projects = Project::factory()->count(2)->create([
            'game_id' => $game->id,
        ]);

        $this->assertCount(2, $game->projects);
        $this->assertTrue($game->projects->contains($projects[0]));
    }
}
