<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Dev;
use Database\factories\DevFactory;
use App\Models\Sale;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GameController extends Controller
{
    // Lista partite dell'utente autenticato
    public function index()
    {
        $games = Auth::user()->games()->get();

        return view('games.index', compact('games'));
    }

    // Crea una nuova partita
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        // Creazione nuova partita associata all'user autenticato 10000 perchè perdevo 
        $game = Game::create([
            'name'       => $request->name,
            'user_id'    => Auth::id(),
            'state'      => 'paused',
            'patrimonio' => 10000,
            'last_update_at' => now(),
        ]);

        // Creo un Dev  e un Sale associato a questa partita
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
    // Pausa partita
    public function pause($id)
    {
        $game = Game::where('user_id', Auth::id())->findOrFail($id);
        $game->update(['state' => 'paused', 'last_update_at' => now()]);
        
        if (request()->expectsJson()) {
            return response()->json(['state' => $game->state, 'message' => 'Partita in pausa']);
        }
        return redirect('/games');
    }

    // Riprendi partita
    public function resume($id)
    {
        $game = Game::where('user_id', Auth::id())->findOrFail($id);
        $game->update(['state' => 'in_progress', 'last_update_at' => now()]);
        
        if (request()->expectsJson()) {
            return response()->json(['state' => $game->state]);
        }
        return redirect("/games/{$game->id}/projects");
    }

    // Mostra dettagli partita (azienda)
    public function show(Game $game){
        $devs = $game->devs;  
        $sales = $game->sales; 
        return view('games.azienda', compact('game', 'devs', 'sales'));
    }
    // Mostra candidati disponibili
    public function showCandidati(Game $game)
    {
    $devs = \App\Models\Dev::whereNull('game_id')->get();
    $sales = \App\Models\Sale::whereNull('game_id')->get();
    return view('games.candidati', compact('devs', 'sales', 'game'));
    }

    // Genera nuovi candidati (dev e sales) fino a un massimo di 50 totali
    public function generaCandidati()
    {
        $game = auth()->user()->games()->orderByDesc('id')->first();
        $currentDevs = \App\Models\Dev::whereNull('game_id')->count();
        $currentSales = \App\Models\Sale::whereNull('game_id')->count();
        $totalCurrent = $currentDevs + $currentSales;
        if($totalCurrent >= 50 )
        {
            return redirect("/games/{$game->id}/candidati")->with('error', "Hai già 50 candidati disponibili!");
        }
        Dev::factory()->count(3)->create();
        Sale::factory()->count(1)->create();
        return redirect("/games/{$game->id}/candidati")->with('success', "Generati 3 dev e 1 sale! Totale candidati disponibili: " . ($currentDevs + 3 + $currentSales + 1));
    }

    //assumi i candidati selezionati
    public function assumiCandidato()
    {
        $game = auth()->user()->games()->orderByDesc('id')->first();
        $type = request('type');
        $id = request('id');

        if ($type === 'dev') {
            $candidato = Dev::findOrFail($id);
        } elseif ($type === 'sale') {
            $candidato = Sale::findOrFail($id);
        } else {
            abort(400, 'Tipo candidato non valido');
        }

        if ($candidato->game_id !== null) {
            abort(400, 'Candidato già assegnato a una partita');
        }

        $candidato->update(['game_id' => $game->id]);
        return redirect("/games/{$game->id}/candidati")->with('success', "Candidato assunto con successo!");
    }
    //aggiotna tutti i progetti in_progress (da chiamare quando non si è dentro i progetti)
    public function updateAllProjects(Game $game)
    {
        $inProgressProjects = $game->projects()->where('status', 'in_progress')->get();
        $updated = 0;
        foreach ($inProgressProjects as $project) {
            $project->updateProgress();
            $updated++;
        }
        return response()->json([
            'success' => true,
            'updated_projects' => $updated
        ]);
    }
    
    // Tick globale: aggiorna tutti i giochi dell'utente in_progress
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
