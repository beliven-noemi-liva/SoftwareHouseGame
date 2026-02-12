<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ProjectController;

Route::get('/', function () {
    // Se l'utente è autenticato (registrato), mostra l'index delle partite
    if(auth()->check()){
        return redirect()->action([\App\Http\Controllers\GameController::class, 'index']);
    }
    // Altrimenti mostra la pagina di login
    return redirect('/login');
});
//rotte pubbliche
Route::get('/register',[AuthController::class,'registerCreate'] );
Route::post('/register',[AuthController::class,'registerStore'] )->middleware('guest');
Route::get('/login',[AuthController::class,'loginCreate'] )->middleware('guest');
Route::post('/login',    [AuthController::class, 'loginStore'])->middleware('guest');
Route::delete('/logout',   [AuthController::class, 'logout'])->middleware('auth');

//funzionalità gioco
Route::middleware('auth')->group(function () {
    Route::get('/games', [GameController::class, 'index']);
    Route::post('/games', [GameController::class, 'store']);
    Route::post('/games/{game}/pause', [GameController::class, 'pause']);
    Route::post('/games/{game}/resume', [GameController::class, 'resume']);
    Route::get('/games/{game}', [GameController::class, 'show']);
});

//candidati
Route::middleware('auth')->group(function () {
    Route::post('/candidati/genera', [GameController::class, 'generaCandidati']);
    Route::get('/games/{game}/candidati', [GameController::class,  'showCandidati']);
    Route::post('/candidati/{candidato}/assumi', [GameController::class, 'assumiCandidato']);
});
//progetti e tick
Route::middleware('auth')->group(function() {
    Route::get('/games/{game}/projects', [ProjectController::class, 'index']);
    Route::get('/games/{game}/projects/{project}', [ProjectController::class, 'show']);
    Route::post('/games/{game}/projects', [ProjectController::class, 'store']);
    Route::post('/games/{game}/projects/{project}/assign-devs', [ProjectController::class, 'assignDevs']);
    //Route::post('/games/{game}/auto-procaccia', [ProjectController::class, 'autoProcaccia']);
    Route::post('/games/{game}/projects/{project}/progress', [ProjectController::class, 'progress']);
    Route::post('/games/{game}/update-all-projects', [\App\Http\Controllers\GameController::class, 'updateAllProjects']);
    Route::post('/games/{game}/tick', [\App\Http\Controllers\GameController::class, 'tick']);
    Route::post('/games/tick-all', [\App\Http\Controllers\GameController::class, 'tickAll']);
});