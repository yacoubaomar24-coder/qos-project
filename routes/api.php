<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VoteController;

Route::prefix('v1')->group(function () {
    // Endpoint pour enregistrer un vote
    Route::post('/votes', [VoteController::class, 'store']);

    // Endpoint pour vérifier un dispositif
    Route::post('/dispositifs/verify', [VoteController::class, 'verify']);
});