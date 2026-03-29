<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\TeamController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware('role:manager,coach')->group(function () {
        Route::apiResource('teams', TeamController::class);
        Route::apiResource('players', PlayerController::class);

        Route::middleware('role:manager')->group(function () {
            Route::post('/teams/{team}/coaches', [TeamController::class, 'assignCoach']);
            Route::delete('/teams/{team}/coaches/{user}', [TeamController::class, 'removeCoach']);
        });
    });
});
