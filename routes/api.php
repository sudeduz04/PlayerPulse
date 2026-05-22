<?php

use App\Http\Controllers\Api\AiAnalysisController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DevelopmentReportController;
use App\Http\Controllers\Api\FixtureImportController;
use App\Http\Controllers\Api\InjuryController;
use App\Http\Controllers\Api\JobStatusController;
use App\Http\Controllers\Api\LeagueController;
use App\Http\Controllers\Api\LineupController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\MatchStatsController;
use App\Http\Controllers\Api\PhysicalMeasurementController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\PlayerHealthController;
use App\Http\Controllers\Api\PlayerMatchHistoryController;
use App\Http\Controllers\Api\PlayerNoteController;
use App\Http\Controllers\Api\PlayerTrainingHistoryController;
use App\Http\Controllers\Api\SmartLineupController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\TrainingController;
use App\Http\Controllers\Api\TrainingPerformanceController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/dashboard', [DashboardController::class, 'show']);

    Route::middleware('role:player')->group(function () {
        Route::get('/my/health', [PlayerHealthController::class, 'index']);
        Route::get('/my/matches', [PlayerMatchHistoryController::class, 'index']);
        Route::get('/my/trainings', [PlayerTrainingHistoryController::class, 'index']);
        Route::get('/my/reports', [DevelopmentReportController::class, 'myReports']);
    });

    Route::middleware('role:super_admin,manager,coach')->group(function () {
        Route::apiResource('teams', TeamController::class)->only(['index', 'show']);
        Route::get('/leagues', [LeagueController::class, 'index']);
        Route::get('/leagues/{league}', [LeagueController::class, 'show']);
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
        Route::apiResource('users', UserController::class);
        Route::post('/teams', [TeamController::class, 'store']);
        Route::delete('/teams/{team}', [TeamController::class, 'destroy']);
        Route::post('/teams/{team}/coaches', [TeamController::class, 'assignCoach']);
        Route::delete('/teams/{team}/coaches/{user}', [TeamController::class, 'removeCoach']);
        Route::post('/teams/{team}/staff', [TeamController::class, 'assignStaff']);
        Route::delete('/teams/{team}/staff/{user}', [TeamController::class, 'removeStaff']);

        Route::apiResource('leagues', LeagueController::class)->only(['store', 'update', 'destroy']);
        Route::post('/leagues/{league}/fixtures/import', [FixtureImportController::class, 'store']);
        Route::get('/fixture-imports/{import}', [FixtureImportController::class, 'show'])->name('api.fixture-imports.show');
    });

    Route::middleware('role:super_admin,manager')->group(function () {
        Route::put('/teams/{team}', [TeamController::class, 'update']);
        Route::patch('/teams/{team}', [TeamController::class, 'update']);
        Route::post('/players/{player}/create-account', [PlayerController::class, 'createAccount']);
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
        Route::get('/players/{player}/notes', [PlayerNoteController::class, 'index']);
        Route::post('/players/{player}/notes', [PlayerNoteController::class, 'store']);
        Route::delete('/notes/{note}', [PlayerNoteController::class, 'destroy']);
    });

    Route::middleware('role:super_admin,coach')->group(function () {
        Route::get('/lineups/options', [LineupController::class, 'options']);
        Route::get('/lineups/{lineup}/status', [SmartLineupController::class, 'status'])->name('api.lineups.status');
        Route::get('/matches/{match}/roster', [LineupController::class, 'roster']);
        Route::apiResource('lineups', LineupController::class)->only(['index', 'store', 'show', 'destroy']);
        Route::get('/smart-squad/options', [SmartLineupController::class, 'options']);
        Route::post('/smart-squad', [SmartLineupController::class, 'store']);
    });

    Route::middleware('role:super_admin,manager,coach')->group(function () {
        Route::get('/analysis/options', [AiAnalysisController::class, 'options']);
        Route::get('/analysis', [AiAnalysisController::class, 'index']);
        Route::get('/analysis/{analysis}/status', [AiAnalysisController::class, 'status'])->name('api.analysis.status');
        Route::get('/analysis/{analysis}', [AiAnalysisController::class, 'show']);
    });

    Route::middleware('role:super_admin,coach')->group(function () {
        Route::post('/analysis', [AiAnalysisController::class, 'store']);
        Route::delete('/analysis/{analysis}', [AiAnalysisController::class, 'destroy']);
    });

    Route::get('/jobs/{uuid}/status', [JobStatusController::class, 'show'])->name('api.jobs.status');
});
