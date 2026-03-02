<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Project;

class ProjectController extends Controller
{
    // List of all the projects
    public function index(Game $game)
    {
        if ($game->user_id != auth()->id()) {
            abort(403);
        }

        $projects = $game->projects()->get();

        return view('games.projects', compact('game', 'projects'));
    }

    // Assign a ready project to available devs
    public function assignDevs(Game $game, Project $project)
    {
        if ($game->user_id != auth()->id() || $project->game_id != $game->id) {
            abort(403);
        }

        $stats = $project->assignAvailableDevs();

        // If the project is not ready or has no devs available, it returns with a message
        if ($stats === null || $stats['devs_assigned'] === 0) {
            return redirect("/games/{$game->id}/projects")
                ->with('warning', 'Non è possibile assegnare dev a questo progetto, sono tutti già impegnati.');
        }

        return redirect("/games/{$game->id}/projects")
            ->with('success', "Progetto assegnato a {$stats['devs_assigned']} dev! Un nuovo progetto è stato creato.");
    }

    // Update the project progress bar and if it is completed update the status to done (call from tick endpoint)
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
            'status'  => $project->status,
            'complex' => $project->complex,
            'name'    => $project->name, ]);
    }
}
