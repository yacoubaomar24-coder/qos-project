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

// Route pour lister les votes
//Route::get('/votes', [VoteController::class, 'index']);

// Route pour créer un vote 
//Route::post('/votes', [VoteController::class, 'store']);

Route::prefix('v1')->group(function () {
    // ✅ Enregistrer un vote — identification par token dans le header
    Route::post('/votes', [VoteController::class, 'store']);

    // ✅ Vérifier le dispositif et récupérer sa config
    Route::get('/dispositifs/info', [VoteController::class, 'check']);
});
