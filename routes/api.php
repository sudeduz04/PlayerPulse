<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DevelopmentReportController;
use App\Http\Controllers\Api\InjuryController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\MatchStatsController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\PhysicalMeasurementController;
use App\Http\Controllers\Api\PlayerHealthController;
use App\Http\Controllers\Api\PlayerMatchHistoryController;
use App\Http\Controllers\Api\PlayerTrainingHistoryController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\TrainingController;
use App\Http\Controllers\Api\TrainingPerformanceController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware('role:player')->group(function () {
        Route::get('/my/health', [PlayerHealthController::class, 'index']);
        Route::get('/my/matches', [PlayerMatchHistoryController::class, 'index']);
        Route::get('/my/trainings', [PlayerTrainingHistoryController::class, 'index']);
        Route::get('/my/reports', [DevelopmentReportController::class, 'myReports']);
    });

    Route::middleware('role:super_admin,manager,coach')->group(function () {
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
        Route::get('/development-reports', [DevelopmentReportController::class, 'index']);
        Route::get('/development-reports/{report}', [DevelopmentReportController::class, 'show']);
        Route::get('/injuries', [InjuryController::class, 'index']);
        Route::get('/physical-measurements', [PhysicalMeasurementController::class, 'index']);
    });

    Route::middleware('role:super_admin')->group(function () {
        Route::post('/teams', [TeamController::class, 'store']);
        Route::delete('/teams/{team}', [TeamController::class, 'destroy']);
        Route::post('/teams/{team}/coaches', [TeamController::class, 'assignCoach']);
        Route::delete('/teams/{team}/coaches/{user}', [TeamController::class, 'removeCoach']);
    });

    Route::middleware('role:super_admin,manager,coach')->group(function () {
        Route::post('/players/{player}/reports', [DevelopmentReportController::class, 'store']);
        Route::delete('/development-reports/{report}', [DevelopmentReportController::class, 'destroy']);
        Route::post('/players/{player}/injuries', [InjuryController::class, 'store']);
        Route::put('/injuries/{injury}', [InjuryController::class, 'update']);
        Route::delete('/injuries/{injury}', [InjuryController::class, 'destroy']);
        Route::post('/players/{player}/measurements', [PhysicalMeasurementController::class, 'store']);
        Route::put('/physical-measurements/{measurement}', [PhysicalMeasurementController::class, 'update']);
        Route::delete('/physical-measurements/{measurement}', [PhysicalMeasurementController::class, 'destroy']);
    });
});
