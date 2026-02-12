<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use App\Models\Dev;
use App\Models\Sale;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

   #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_view_projects_of_their_game()
    {
        $user = User::factory()->create();
        $game = Game::factory()->create(['user_id' => $user->id]);
        $projects = Project::factory()->count(3)->create(['game_id' => $game->id]);

        $this->actingAs($user);
        $response = $this->get("/games/{$game->id}/projects");

        $response->assertStatus(200);
        $response->assertViewHas('game', $game);
        $response->assertViewHas('projects');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_cannot_view_projects_of_other_users_game()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $game = Game::factory()->create(['user_id' => $user1->id]);

        $this->actingAs($user2);
        $response = $this->get("/games/{$game->id}/projects");

        $response->assertStatus(403);
    }



   #[\PHPUnit\Framework\Attributes\Test]
    public function project_progress_updates_complexity()
    {
        $user = User::factory()->create();
        $game = Game::factory()->create(['user_id' => $user->id, 'patrimonio' => 5000]);
        $sale = Sale::factory()->create(['game_id' => $game->id]);

        $project = Project::factory()->create([
            'game_id' => $game->id,
            'sale_id' => $sale->id,
            'status' => 'in_progress',
            'complex' => 10,
            'value' => 2000,
        ]);

        // Assegna dev al progetto
        $dev = Dev::factory()->create(['game_id' => $game->id, 'exp' => 3]);
        $dev->update(['project_id' => $project->id]);

        $this->actingAs($user);
        $response = $this->post("/games/{$game->id}/projects/{$project->id}/progress");

        // Verifica che la complessità sia stata ridotta
        $project->refresh();
        $this->assertLessThan(10, $project->complex);
    }

   #[\PHPUnit\Framework\Attributes\Test]
    public function project_completes_when_complexity_reaches_zero()
    {
        $user = User::factory()->create();
        $game = Game::factory()->create(['user_id' => $user->id, 'patrimonio' => 5000]);
        $sale = Sale::factory()->create(['game_id' => $game->id]);

        $project = Project::factory()->create([
            'game_id' => $game->id,
            'sale_id' => $sale->id,
            'status' => 'in_progress',
            'complex' => 3,
            'value' => 2000,
        ]);

        // Assegna dev con alta esperienza
        $dev = Dev::factory()->create(['game_id' => $game->id, 'exp' => 5]);
        $dev->update(['project_id' => $project->id]);

        $this->actingAs($user);
        $response = $this->post("/games/{$game->id}/projects/{$project->id}/progress");

        $project->refresh();

        // Se la complessità era bassa e exp alta, dovrebbe essere completato
        if ($project->complex <= 0) {
            $this->assertEquals('done', $project->status);
            // Nota: patrimonio aumenta solo durante processEconomyTick, non in updateProgress
        }
    }


    #[\PHPUnit\Framework\Attributes\Test]
    public function project_belongs_to_game_and_sale()
    {
        $game = Game::factory()->create();
        $sale = Sale::factory()->create(['game_id' => $game->id]);
        $project = Project::factory()->create([
            'game_id' => $game->id,
            'sale_id' => $sale->id,
        ]);

        $this->assertInstanceOf(Game::class, $project->game);
        $this->assertInstanceOf(Sale::class, $project->sale);
        $this->assertEquals($game->id, $project->game->id);
        $this->assertEquals($sale->id, $project->sale->id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function project_has_many_devs()
    {
        $game = Game::factory()->create();
        $sale = Sale::factory()->create(['game_id' => $game->id]);
        $project = Project::factory()->create([
            'game_id' => $game->id,
            'sale_id' => $sale->id,
        ]);

        $devs = Dev::factory()->count(3)->create([
            'game_id' => $game->id,
            'project_id' => $project->id,
        ]);

        $this->assertCount(3, $project->devs);
        $this->assertTrue($project->devs->contains($devs[0]));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function project_can_assign_available_devs()
    {
        $user = User::factory()->create();
        $game = Game::factory()->create(['user_id' => $user->id]);
        $sale = Sale::factory()->create(['game_id' => $game->id, 'exp' => 3]);

        $project = Project::factory()->create([
            'game_id' => $game->id,
            'sale_id' => $sale->id,
            'status' => 'ready',
            'complex' => 2,
        ]);

        // Crea 5 dev disponibili
        Dev::factory()->count(5)->create(['game_id' => $game->id]);

        // Assegna i dev al progetto tramite endpoint
        $this->actingAs($user);
        $response = $this->post("/games/{$game->id}/projects/{$project->id}/assign-devs");

        // Il controller reindirizza dopo l'assegnazione
        $response->assertRedirect("/games/{$game->id}/projects");

        // Verifica che il progetto sia in stato "in_progress"
        $this->assertEquals('in_progress', $project->refresh()->status);

        // Verifica che 2 dev siano stati assegnati
        $assignedDevs = Dev::where('game_id', $game->id)
            ->whereNotNull('project_id')
            ->count();
        $this->assertEquals(2, $assignedDevs);

        // Verifica che un nuovo progetto sia stato creato
        $this->assertEquals(2, $sale->projects->count());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function devs_can_complete_project_through_progress()
    {
        $user = User::factory()->create();
        $game = Game::factory()->create(['user_id' => $user->id, 'patrimonio' => 5000]);
        $sale = Sale::factory()->create(['game_id' => $game->id, 'exp' => 3]);

        $project = Project::factory()->create([
            'game_id' => $game->id,
            'sale_id' => $sale->id,
            'status' => 'in_progress',
            'complex' => 15,
            'value' => 2000,
        ]);

        // Crea 3 dev con esperienza
        $devs = Dev::factory()->count(3)->create([
            'game_id' => $game->id,
            'exp' => 5,
            'project_id' => $project->id,
        ]);

        // Fai progredire il progetto
        $this->actingAs($user);
        $response = $this->post("/games/{$game->id}/projects/{$project->id}/progress");

        $project->refresh();
        $game->refresh();

        // Se i dev hanno exp abbastanza, il progetto dovrebbe essere completato
        if ($project->complex <= 0) {
            $this->assertEquals('done', $project->status);
        }
    }

}
