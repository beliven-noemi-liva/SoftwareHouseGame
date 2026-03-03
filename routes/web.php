<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Se l'utente è autenticato (registrato), mostra l'index delle partite
    if (auth()->check()) {
        return redirect()->action([\App\Http\Controllers\GameController::class, 'index']);
    }

    // Altrimenti mostra la pagina di login
    return redirect('/login');
});
// rotte pubbliche
Route::get('/register', [AuthController::class, 'registerCreate']);
Route::post('/register', [AuthController::class, 'registerStore'])->middleware('guest');
Route::get('/login', [AuthController::class, 'loginCreate'])->middleware('guest');
Route::post('/login', [AuthController::class, 'loginStore'])->middleware('guest');
Route::delete('/logout', [AuthController::class, 'logout'])->middleware('auth');

// funzionalità gioco
Route::middleware('auth')->group(function () {
    Route::get('/games', [GameController::class, 'index']);
    Route::post('/games', [GameController::class, 'store']);
    Route::post('/games/{game}/pause', [GameController::class, 'pause']);
    Route::post('/games/{game}/resume', [GameController::class, 'resume']);
    Route::get('/games/{game}', [GameController::class, 'show']);
});

// candidates
Route::middleware('auth')->group(function () {
    Route::post('/{game}/candidates/create', [GameController::class, 'createCandidate']);
    Route::get('/games/{game}/candidates', [GameController::class,  'showCandidates']);
    Route::post('/{game}/candidates/{candidate}/hire', [GameController::class, 'hireCandidate']);
});
// project e tick
Route::middleware('auth')->group(function () {
    Route::get('/games/{game}/projects', [ProjectController::class, 'index']);
    Route::get('/games/{game}/projects/{project}', [ProjectController::class, 'show']);
    Route::post('/games/{game}/projects', [ProjectController::class, 'store']);
    Route::post('/games/{game}/projects/{project}/assign-devs', [ProjectController::class, 'assignDevs']);
    // Route::post('/games/{game}/auto-procaccia', [ProjectController::class, 'autoProcaccia']);
    Route::post('/games/{game}/projects/{project}/progress', [ProjectController::class, 'progress']);
    Route::post('/games/{game}/update-all-projects', [\App\Http\Controllers\GameController::class, 'updateAllProjects']);
    Route::post('/games/{game}/tick', [\App\Http\Controllers\GameController::class, 'tick']);
    Route::post('/games/tick-all', [\App\Http\Controllers\GameController::class, 'tickAll']);
});
// update the statistics of the game
Route::get('api/games/{game}/statistics', function (\App\Models\Game $game) {
    $game->processEconomyTick();

    return $game->only(['id', 'name', 'patrimonio', 'state']);
});

// catch the data of the games
Route::get('api/games/{game}/datas', function (\App\Models\Game $game) {
    return $game->only(['id', 'name', 'patrimonio', 'state']);
});
