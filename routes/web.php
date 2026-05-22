<?php

use App\Http\Controllers\Web\AiAnalysisController;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\DevelopmentReportController;
use App\Http\Controllers\Web\EvaluationController;
use App\Http\Controllers\Web\InjuryController;
use App\Http\Controllers\Web\LeagueController;
use App\Http\Controllers\Web\LineupController;
use App\Http\Controllers\Web\MatchController;
use App\Http\Controllers\Web\MatchStatsController;
use App\Http\Controllers\Web\PhysicalMeasurementController;
use App\Http\Controllers\Web\PlayerController;
use App\Http\Controllers\Web\PlayerHealthController;
use App\Http\Controllers\Web\PlayerMatchHistoryController;
use App\Http\Controllers\Web\PlayerNoteController;
use App\Http\Controllers\Web\PlayerReportController;
use App\Http\Controllers\Web\PlayerTrainingHistoryController;
use App\Http\Controllers\Web\SmartLineupController;
use App\Http\Controllers\Web\TeamController;
use App\Http\Controllers\Web\TrainingController;
use App\Http\Controllers\Web\TrainingPerformanceController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Tüm rollerin read-only erişebileceği fikstür sayfaları
    Route::get('/fixtures', [LeagueController::class, 'publicIndex'])->name('fixtures.index');
    Route::get('/fixtures/{league}', [LeagueController::class, 'publicShow'])->name('fixtures.show');

    // Root redirect based on role
    Route::get('/', function () {
        return match (auth()->user()->role) {
            'super_admin' => redirect()->route('super_admin.dashboard'),
            'coach' => redirect()->route('coach.dashboard'),
            'manager' => redirect()->route('manager.dashboard'),
            'player' => redirect()->route('player.dashboard'),
            default => redirect()->route('login'),
        };
    })->name('home');

    // Super Admin routes
    Route::middleware('role:super_admin')->prefix('super-admin')->name('super_admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'superAdmin'])->name('dashboard');
        Route::resource('users', UserController::class);
        Route::resource('teams', TeamController::class);
        Route::resource('leagues', LeagueController::class);
        Route::post('/leagues/{league}/fixtures', [LeagueController::class, 'import'])->name('leagues.fixtures.import');
        Route::get('/leagues/{league}/imports/{import}/status', [LeagueController::class, 'importStatus'])->name('leagues.imports.status');
        Route::post('/teams/{team}/staff', [TeamController::class, 'assignStaff'])->name('teams.assign-staff');
        Route::delete('/teams/{team}/staff/{user}', [TeamController::class, 'removeStaff'])->name('teams.remove-staff');
        Route::resource('players', PlayerController::class);
        Route::resource('trainings', TrainingController::class);
        Route::resource('matches', MatchController::class);
        Route::resource('evaluations', EvaluationController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
        Route::get('/trainings/{training}/performances', [TrainingPerformanceController::class, 'edit'])->name('trainings.performances.edit');
        Route::put('/trainings/{training}/performances', [TrainingPerformanceController::class, 'update'])->name('trainings.performances.update');
        Route::get('/matches/{match}/stats', [MatchStatsController::class, 'edit'])->name('matches.stats.edit');
        Route::put('/matches/{match}/stats', [MatchStatsController::class, 'update'])->name('matches.stats.update');
        Route::post('/players/{player}/create-account', [PlayerController::class, 'createAccount'])->name('players.create-account');
        Route::post('/players/{player}/injuries', [InjuryController::class, 'store'])->name('players.injuries.store');
        Route::put('/players/{player}/injuries/{injury}', [InjuryController::class, 'update'])->name('players.injuries.update');
        Route::delete('/players/{player}/injuries/{injury}', [InjuryController::class, 'destroy'])->name('players.injuries.destroy');
        Route::post('/players/{player}/measurements', [PhysicalMeasurementController::class, 'store'])->name('players.measurements.store');
        Route::delete('/players/{player}/measurements/{measurement}', [PhysicalMeasurementController::class, 'destroy'])->name('players.measurements.destroy');
        Route::post('/players/{player}/notes', [PlayerNoteController::class, 'store'])->name('players.notes.store');
        Route::delete('/players/{player}/notes/{note}', [PlayerNoteController::class, 'destroy'])->name('players.notes.destroy');
        Route::post('/players/{player}/reports', [DevelopmentReportController::class, 'store'])->name('players.reports.store');
        Route::delete('/players/{player}/reports/{report}', [DevelopmentReportController::class, 'destroy'])->name('players.reports.destroy');
    });

    // Coach routes
    Route::middleware('role:coach')->prefix('coach')->name('coach.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'coach'])->name('dashboard');
        Route::resource('teams', TeamController::class)->only(['index', 'show']);
        Route::resource('players', PlayerController::class);
        Route::resource('evaluations', EvaluationController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
        Route::resource('trainings', TrainingController::class);
        Route::get('/trainings/{training}/performances', [TrainingPerformanceController::class, 'edit'])->name('trainings.performances.edit');
        Route::put('/trainings/{training}/performances', [TrainingPerformanceController::class, 'update'])->name('trainings.performances.update');
        Route::resource('matches', MatchController::class);
        Route::get('/matches/{match}/stats', [MatchStatsController::class, 'edit'])->name('matches.stats.edit');
        Route::put('/matches/{match}/stats', [MatchStatsController::class, 'update'])->name('matches.stats.update');

        // Player sub-resources
        Route::post('/players/{player}/injuries', [InjuryController::class, 'store'])->name('players.injuries.store');
        Route::put('/players/{player}/injuries/{injury}', [InjuryController::class, 'update'])->name('players.injuries.update');
        Route::delete('/players/{player}/injuries/{injury}', [InjuryController::class, 'destroy'])->name('players.injuries.destroy');
        Route::post('/players/{player}/measurements', [PhysicalMeasurementController::class, 'store'])->name('players.measurements.store');
        Route::delete('/players/{player}/measurements/{measurement}', [PhysicalMeasurementController::class, 'destroy'])->name('players.measurements.destroy');
        Route::post('/players/{player}/notes', [PlayerNoteController::class, 'store'])->name('players.notes.store');
        Route::delete('/players/{player}/notes/{note}', [PlayerNoteController::class, 'destroy'])->name('players.notes.destroy');
        Route::post('/players/{player}/reports', [DevelopmentReportController::class, 'store'])->name('players.reports.store');
        Route::delete('/players/{player}/reports/{report}', [DevelopmentReportController::class, 'destroy'])->name('players.reports.destroy');

        // Lineup builder
        Route::resource('lineups', LineupController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

        // AI smart lineup
        Route::get('/smart-squad', [SmartLineupController::class, 'create'])->name('smart-squad.create');
        Route::post('/smart-squad', [SmartLineupController::class, 'store'])->name('smart-squad.store');
        Route::get('/smart-squad/{lineup}/status', [SmartLineupController::class, 'status'])->name('smart-squad.status');

        // AI analysis panel
        Route::get('/analysis', [AiAnalysisController::class, 'index'])->name('analysis.index');
        Route::get('/analysis/create', [AiAnalysisController::class, 'create'])->name('analysis.create');
        Route::post('/analysis', [AiAnalysisController::class, 'store'])->name('analysis.store');
        Route::get('/analysis/{analysis}/status', [AiAnalysisController::class, 'status'])->name('analysis.status');
        Route::get('/analysis/{analysis}', [AiAnalysisController::class, 'show'])->name('analysis.show');
        Route::delete('/analysis/{analysis}', [AiAnalysisController::class, 'destroy'])->name('analysis.destroy');
    });

    // Manager routes
    Route::middleware('role:manager')->prefix('manager')->name('manager.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'manager'])->name('dashboard');
        Route::resource('teams', TeamController::class)->only(['index', 'show', 'edit', 'update']);
        Route::resource('players', PlayerController::class);
        Route::post('/players/{player}/create-account', [PlayerController::class, 'createAccount'])->name('players.create-account');
        Route::resource('evaluations', EvaluationController::class)->only(['index', 'show']);
        Route::resource('trainings', TrainingController::class)->only(['index', 'show']);
        Route::resource('matches', MatchController::class)->only(['index', 'show']);

        // Player sub-resources
        Route::post('/players/{player}/injuries', [InjuryController::class, 'store'])->name('players.injuries.store');
        Route::put('/players/{player}/injuries/{injury}', [InjuryController::class, 'update'])->name('players.injuries.update');
        Route::delete('/players/{player}/injuries/{injury}', [InjuryController::class, 'destroy'])->name('players.injuries.destroy');
        Route::post('/players/{player}/measurements', [PhysicalMeasurementController::class, 'store'])->name('players.measurements.store');
        Route::delete('/players/{player}/measurements/{measurement}', [PhysicalMeasurementController::class, 'destroy'])->name('players.measurements.destroy');
        Route::post('/players/{player}/notes', [PlayerNoteController::class, 'store'])->name('players.notes.store');
        Route::delete('/players/{player}/notes/{note}', [PlayerNoteController::class, 'destroy'])->name('players.notes.destroy');
        Route::post('/players/{player}/reports', [DevelopmentReportController::class, 'store'])->name('players.reports.store');
        Route::delete('/players/{player}/reports/{report}', [DevelopmentReportController::class, 'destroy'])->name('players.reports.destroy');

        // AI analysis (read-only for manager)
        Route::get('/analysis', [AiAnalysisController::class, 'index'])->name('analysis.index');
        Route::get('/analysis/{analysis}/status', [AiAnalysisController::class, 'status'])->name('analysis.status');
        Route::get('/analysis/{analysis}', [AiAnalysisController::class, 'show'])->name('analysis.show');
    });

    // Player routes
    Route::middleware('role:player')->prefix('player')->name('player.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'player'])->name('dashboard');
        Route::get('/health', [PlayerHealthController::class, 'index'])->name('health.index');
        Route::get('/matches', [PlayerMatchHistoryController::class, 'index'])->name('matches.index');
        Route::get('/trainings', [PlayerTrainingHistoryController::class, 'index'])->name('trainings.index');
        Route::get('/reports', [PlayerReportController::class, 'index'])->name('reports.index');
    });
});
