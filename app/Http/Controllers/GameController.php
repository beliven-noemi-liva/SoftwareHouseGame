<?php

namespace App\Http\Controllers;

use App\Models\Dev;
use App\Models\Game;
use App\Models\Project;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    // Authenticated user's game list
    public function index()
    {
        $games = Auth::user()->games()->get();

        return view('games.index', compact('games'));
    }

    // Create a new game
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        // Creating a new game associated with authenticated user, 10000 because I always lost
        $game = Game::create([
            'name'           => $request->name,
            'user_id'        => Auth::id(),
            'state'          => 'paused',
            'patrimonio'     => 10000,
            'last_update_at' => now(),
        ]);

        // create a dev and a sale for the game
        Dev::factory()->create([
            'game_id' => $game->id,
        ]);
        $sale = Sale::factory()->create([
            'game_id' => $game->id,
        ]);
        // $sale->procacciaProgetto($game); no perchè a lo sleep
        $project = Project::factory()->create([
            'game_id' => $game->id,
            'sale_id' => $sale->id,
        ]);

        return redirect('/games');
    }

    // Pause the game
    public function pause($id)
    {
        $game = Game::where('user_id', Auth::id())->findOrFail($id);
        $game->update(['state' => 'paused', 'last_update_at' => now()]);

        if (request()->expectsJson()) {
            return response()->json(['state' => $game->state, 'message' => 'Partita in pausa']);
        }

        return redirect('/games');
    }

    // Resume the game
    public function resume($id)
    {
        $game = Game::where('user_id', Auth::id())->findOrFail($id);
        $game->update(['state' => 'in_progress', 'last_update_at' => now()]);

        if (request()->expectsJson()) {
            return response()->json(['state' => $game->state]);
        }

        return redirect("/games/{$game->id}/projects");
    }

    // Show game's detail (azienda)
    public function show(Game $game)
    {
        $devs = $game->devs;
        $sales = $game->sales;

        return view('games.azienda', compact('game', 'devs', 'sales'));
    }

    // Show cadindates
    public function showCandidates(Game $game)
    {
        $devs = \App\Models\Dev::whereNull('game_id')->get();
        $sales = \App\Models\Sale::whereNull('game_id')->get();

        return view('games.candidati', compact('devs', 'sales', 'game'));
    }

    // create new candidates (dev e sales) max of 50
    public function createCandidate(Game $game)
    {
        $currentDevs = \App\Models\Dev::whereNull('game_id')->count();
        $currentSales = \App\Models\Sale::whereNull('game_id')->count();
        $totalCurrent = $currentDevs + $currentSales;
        if ($totalCurrent >= 50) {
            return redirect("/games/{$game->id}/candidates")->with('error', 'Hai già 50 candidati disponibili!');
        }
        Dev::factory()->count(3)->create();
        Sale::factory()->count(1)->create();

        return redirect("/games/{$game->id}/candidates")->with('success', 'Generati 3 dev e 1 sale! Totale candidati disponibili: ' . ($currentDevs + 3 + $currentSales + 1));
    }

    // hires the candidate
    public function hireCandidate(Game $game)
    {
        $type = request('type');
        $id = request('id');

        if ($type === 'dev') {
            $candidate = Dev::findOrFail($id);
        } elseif ($type === 'sale') {
            $candidate = Sale::findOrFail($id);
        } else {
            abort(400, 'Tipo candidato non valido');
        }

        if ($candidate->game_id !== null) {
            abort(400, 'Candidato già assegnato a una partita');
        }

        $candidate->update(['game_id' => $game->id]);

        return redirect("/games/{$game->id}/candidates")->with('success', 'Candidato assunto con successo!');
    }

    // Update all in_progress projects (call when not in projects)
    public function updateAllProjects(Game $game)
    {
        $inProgressProjects = $game->projects()->where('status', 'in_progress')->get();
        $updated = 0;
        foreach ($inProgressProjects as $project) {
            $project->updateProgress();
            $updated++;
        }

        return response()->json([
            'success'          => true,
            'updated_projects' => $updated,
        ]);
    }

    // Global tick: Update all user's games in progress
    public function tickAll()
    {
        $games = Auth::user()->games()->where('state', 'in_progress')->get();
        $results = [];

        foreach ($games as $game) {
            $results[$game->id] = $game->processEconomyTick();
        }

        return response()->json(['games' => $results]);
    }
}
