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

// Endpoint pour créer un vote
Route::post('/votes', [VoteController::class, 'store']);
    
// Endpoint pour vérifier un dispositif
Route::post('/dispositifs/verify', [VoteController::class, 'verify']);