<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Project;
use App\Models\Dev;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    // Elenco di tutti i progetti di una partita
    public function index(Game $game)
    {
        if ($game->user_id != auth()->id()) {
            abort(403);
        }
        $projects = $game->projects()->get();
        return view('games.projects', compact('game', 'projects'));
    }

    //Assegna un progetto in stato ready ai devs disponibili
    public function assignDevs(Game $game, Project $project)
    {
        if ($game->user_id != auth()->id() || $project->game_id != $game->id) {
            abort(403);
        }

        $stats = $project->assignAvailableDevs();

        // Se il progetto non è ready o non ha dev disponibili, ritorna con messaggio
        if ($stats === null || $stats['devs_assigned'] === 0) {
            return redirect("/games/{$game->id}/projects")
                ->with('warning', 'Non è possibile assegnare dev a questo progetto, sono tutti già impegnati.');
        }
        return redirect("/games/{$game->id}/projects")
            ->with('success', "Progetto assegnato a {$stats['devs_assigned']} dev! Un nuovo progetto è stato creato.");
    }

    // Aggiorna la barra di progresso del progetto e se è completato aggiorna lo stato a done (chiamata da endpoint di tick)
    public function progress(Game $game, Project $project)
    {
        if ($game->user_id != auth()->id() || $project->game_id != $game->id) {
            abort(403);
        }

        // Usa il metodo del modello per aggiornare il progresso
        $result = $project->updateProgress();

        if (!$result['updated']) {
            return response()->json(['error' => $result['message']], 400);
        }
        return response()->json([
        'status' => $project->status,
        'complex' => $project->complex,
        'name' => $project->name, ]);
    }

    //autoprocaccia dei sale in modo da non dover aspettare quando si assegna il progetto
    /*public function autoProcaccia(Game $game)
    {
        if ($game->user_id != auth()->id()) {
            abort(403);
        }
        $sales = $game->sales()->whereNull('project_id')->get();
        foreach($sales as $sale)
        {
            $sale->procacciaProgetto($game);
        }
        return response()->json(['success' => true, 'message' => 'Auto procaccia eseguito']);
    }*/
}