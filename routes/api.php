<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\MatchStatsController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\TrainingController;
use App\Http\Controllers\Api\TrainingPerformanceController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware('role:manager,coach')->group(function () {
        Route::apiResource('teams', TeamController::class)->only(['index', 'show', 'update']);
        Route::apiResource('players', PlayerController::class);
        Route::apiResource('trainings', TrainingController::class);
        Route::get('/trainings/{training}/performances', [TrainingPerformanceController::class, 'index']);
        Route::post('/trainings/{training}/performances', [TrainingPerformanceController::class, 'store']);
        Route::post('/trainings/{training}/performances/bulk', [TrainingPerformanceController::class, 'bulkStore']);
        Route::apiResource('matches', MatchController::class);
        Route::get('/matches/{match}/stats', [MatchStatsController::class, 'index']);
        Route::post('/matches/{match}/stats', [MatchStatsController::class, 'store']);
        Route::post('/matches/{match}/stats/bulk', [MatchStatsController::class, 'bulkStore']);
    });

    Route::middleware('role:manager')->group(function () {
        Route::post('/teams', [TeamController::class, 'store']);
        Route::delete('/teams/{team}', [TeamController::class, 'destroy']);
        Route::post('/teams/{team}/coaches', [TeamController::class, 'assignCoach']);
        Route::delete('/teams/{team}/coaches/{user}', [TeamController::class, 'removeCoach']);
    });
});
