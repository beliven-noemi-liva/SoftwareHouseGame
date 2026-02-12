<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Dev;
use App\Models\Game;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameTest extends TestCase
{
    use RefreshDatabase;
    #[\PHPUnit\Framework\Attributes\Test]
    public function an_authenticated_user_can_create_a_game()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $game = Game::create([
            'name' => 'Test Game',
            'user_id' => $user->id,
            'state' => 'in_progress',
            'patrimonio' => 5000,
            'last_update_at' => now(),
        ]);

        $this->assertDatabaseHas('games', [
            'user_id' => $user->id,
            'patrimonio' => 5000,
            'state' => 'in_progress',
        ]);
    }

   #[\PHPUnit\Framework\Attributes\Test]
    public function creating_a_game_also_creates_a_dev_and_a_sale()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post('/games', [
            'name' => 'Test Game',
        ]);

        // Prendi l'id della partita dalla risposta)
        $gameId = Game::first()->id;

        // Esattamente un dev e un sale con questo game_id
        $this->assertEquals(1, Dev::where('game_id', $gameId)->count());
        $this->assertEquals(1, Sale::where('game_id', $gameId)->count());

        // Sono proprio in tabella!
        $this->assertDatabaseHas('devs', [
            'game_id' => $gameId,
        ]);
        $this->assertDatabaseHas('sales', [
            'game_id' => $gameId,
        ]);
    }
}