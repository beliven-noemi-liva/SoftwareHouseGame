<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Game;
use App\Models\Dev;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameTickTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function patrimonio_decreases_every_tick_when_game_is_in_progress()
    {
        // Crea un utente e autentica
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crea un gioco in stato in_progress con patrimonio iniziale
        $game = Game::create([
            'name' => 'Test Game',
            'user_id' => $user->id,
            'state' => 'in_progress',
            'patrimonio' => 50000,
            'last_update_at' => now(),
        ]);

        // Crea un dev e un sale con stipendi noti
        Dev::factory()->create([
            'game_id' => $game->id,
            'stipendio' => 1000, // 1000 € di stipendio
            'exp' => 5,
        ]);

        Sale::factory()->create([
            'game_id' => $game->id,
            'stipendio' => 500, // 500 € di stipendio
            'exp' => 3,
        ]);

        // Patrimonio iniziale
        $initialPatrimonio = $game->patrimonio;
        $this->assertEquals(50000, $initialPatrimonio);

        // Primo tick
        $response = $this->postJson('/games/tick-all');
        $response->assertStatus(200);
        
        $game->refresh();
        $firstTickPatrimonio = $game->patrimonio;
        
        // Verifica che il patrimonio è diminuito (dev 1000 + sale 500 = 1500)
        $this->assertEquals(50000 - 1500, $firstTickPatrimonio);
        $this->assertLessThan($initialPatrimonio, $firstTickPatrimonio);

        // Secondo tick
        $response = $this->postJson('/games/tick-all');
        $response->assertStatus(200);
        
        $game->refresh();
        $secondTickPatrimonio = $game->patrimonio;
        
        // Verifica che il patrimonio è diminuito di nuovo
        $this->assertEquals(50000 - 3000, $secondTickPatrimonio);
        $this->assertLessThan($firstTickPatrimonio, $secondTickPatrimonio);

        // Terzo tick
        $response = $this->postJson('/games/tick-all');
        $response->assertStatus(200);
        
        $game->refresh();
        $thirdTickPatrimonio = $game->patrimonio;
        
        // Verifica che il patrimonio è diminuito di nuovo
        $this->assertEquals(50000 - 4500, $thirdTickPatrimonio);
        $this->assertLessThan($secondTickPatrimonio, $thirdTickPatrimonio);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function tick_all_returns_updated_patrimonio_for_all_games()
    {
        // Crea un utente
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crea due giochi in stato in_progress
        $game1 = Game::create([
            'name' => 'Game 1',
            'user_id' => $user->id,
            'state' => 'in_progress',
            'patrimonio' => 10000,
            'last_update_at' => now(),
        ]);

        $game2 = Game::create([
            'name' => 'Game 2',
            'user_id' => $user->id,
            'state' => 'in_progress',
            'patrimonio' => 20000,
            'last_update_at' => now(),
        ]);

        // Crea dev e sale per game1
        Dev::factory()->create([
            'game_id' => $game1->id,
            'stipendio' => 100,
        ]);

        Sale::factory()->create([
            'game_id' => $game1->id,
            'stipendio' => 50,
        ]);

        // Crea dev e sale per game2
        Dev::factory()->create([
            'game_id' => $game2->id,
            'stipendio' => 200,
        ]);

        Sale::factory()->create([
            'game_id' => $game2->id,
            'stipendio' => 100,
        ]);

        // Chiama l'endpoint tick-all
        $response = $this->postJson('/games/tick-all');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'games' => [
                $game1->id => ['patrimonio', 'total_salaries', 'collected', 'state'],
                $game2->id => ['patrimonio', 'total_salaries', 'collected', 'state'],
            ]
        ]);

        // Verifica che il patrimonio di game1 è diminuito correttamente
        $this->assertEquals(10000 - 150, $response->json("games.{$game1->id}.patrimonio"));

        // Verifica che il patrimonio di game2 è diminuito correttamente
        $this->assertEquals(20000 - 300, $response->json("games.{$game2->id}.patrimonio"));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function game_state_becomes_finish_when_patrimonio_reaches_zero()
    {
        // Crea un utente
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crea un gioco con patrimonio molto basso
        $game = Game::create([
            'name' => 'Test Game',
            'user_id' => $user->id,
            'state' => 'in_progress',
            'patrimonio' => 1000,
            'last_update_at' => now(),
        ]);

        // Crea dev e sale con stipendi alti
        Dev::factory()->create([
            'game_id' => $game->id,
            'stipendio' => 600,
        ]);

        Sale::factory()->create([
            'game_id' => $game->id,
            'stipendio' => 500,
        ]);

        // Chiama il tick
        $response = $this->postJson('/games/tick-all');
        $response->assertStatus(200);

        $game->refresh();

        // Verifica che lo stato è diventato 'finish'
        $this->assertEquals('finish', $game->state);
        $this->assertLessThanOrEqual(0, $game->patrimonio);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function game_patrimonio_changes_only_based_on_its_own_devs_and_projects()
    {
        // Crea un utente
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crea due giochi per lo stesso utente
        $game1 = Game::create([
            'name' => 'Game 1',
            'user_id' => $user->id,
            'state' => 'in_progress',
            'patrimonio' => 10000,
            'last_update_at' => now(),
        ]);

        $game2 = Game::create([
            'name' => 'Game 2',
            'user_id' => $user->id,
            'state' => 'in_progress',
            'patrimonio' => 20000,
            'last_update_at' => now(),
        ]);

        // Crea dev e sale SOLO per game1 (con stipendi alti)
        Dev::factory()->create([
            'game_id' => $game1->id,
            'stipendio' => 500,
        ]);

        Sale::factory()->create([
            'game_id' => $game1->id,
            'stipendio' => 300,
        ]);

        // Crea dev e sale SOLO per game2 (con stipendi molto diversi)
        Dev::factory()->create([
            'game_id' => $game2->id,
            'stipendio' => 1000,
        ]);

        Sale::factory()->create([
            'game_id' => $game2->id,
            'stipendio' => 2000,
        ]);

        // Salva i patrimoni iniziali
        $game1InitialPatrimonio = $game1->patrimonio;
        $game2InitialPatrimonio = $game2->patrimonio;

        // Chiama tick-all
        $response = $this->postJson('/games/tick-all');
        $response->assertStatus(200);

        // Verifica game1
        $game1->refresh();
        // Game1 dovrebbe diminuire solo di (500 + 300 = 800)
        $this->assertEquals($game1InitialPatrimonio - 800, $game1->patrimonio);
        $this->assertEquals(10000 - 800, $game1->patrimonio);

        // Verifica game2
        $game2->refresh();
        // Game2 dovrebbe diminuire solo di (1000 + 2000 = 3000), NON di (500 + 300)
        $this->assertEquals($game2InitialPatrimonio - 3000, $game2->patrimonio);
        $this->assertEquals(20000 - 3000, $game2->patrimonio);

        // Verifica che game1 non sia stato influenzato da game2
        $this->assertNotEquals($game1->patrimonio, $game2InitialPatrimonio - 800 - 3000);
        
        // Verifica che i patrimoni siano indipendenti
        $this->assertNotEquals(
            $game1->patrimonio,
            $game1InitialPatrimonio - 3000,
            'Game1 patrimonio deve perdere solo i suoi stipendi, non quelli di game2'
        );
    }
}
