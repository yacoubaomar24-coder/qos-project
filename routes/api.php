<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VoteController;

/*
// versionnement API, 
Route::prefix('v1')->group(function () {
    
    // Votes CRUD
    //Route::get('/votes',          [VoteController::class, 'index']);

    // Endpoint pour créer un vote
    Route::post('/votes',         [VoteController::class, 'store']);
    
    //Route::get('/votes/stats',    [VoteController::class, 'stats']);
    //Route::get('/votes/par-site', [VoteController::class, 'parSite']);
    //Route::get('/votes/{id}',     [VoteController::class, 'show']);

    // Endpoint pour vérifier un dispositif
    Route::post('/dispositifs/verify', [VoteController::class, 'verify']);
});*/

// Route pour créer un vote, protégée (seul l'objet IoT avec un token valide peut envoyer des données)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/votes', [VoteController::class, 'store']);
});
    
// Route pour vérifier un dispositif
Route::post('/dispositifs/verify', [VoteController::class, 'verify']);