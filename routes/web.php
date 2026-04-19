<?php

use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\MatchController;
use App\Http\Controllers\Web\MatchStatsController;
use App\Http\Controllers\Web\PlayerController;
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
        Route::post('/teams/{team}/staff', [TeamController::class, 'assignStaff'])->name('teams.assign-staff');
        Route::delete('/teams/{team}/staff/{user}', [TeamController::class, 'removeStaff'])->name('teams.remove-staff');
        Route::resource('players', PlayerController::class);
        Route::post('/players/{player}/create-account', [PlayerController::class, 'createAccount'])->name('players.create-account');
    });

    // Coach routes
    Route::middleware('role:coach')->prefix('coach')->name('coach.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'coach'])->name('dashboard');
        Route::resource('teams', TeamController::class)->only(['index', 'show']);
        Route::resource('players', PlayerController::class);
        Route::resource('trainings', TrainingController::class);
        Route::get('/trainings/{training}/performances', [TrainingPerformanceController::class, 'edit'])->name('trainings.performances.edit');
        Route::put('/trainings/{training}/performances', [TrainingPerformanceController::class, 'update'])->name('trainings.performances.update');
        Route::resource('matches', MatchController::class);
        Route::get('/matches/{match}/stats', [MatchStatsController::class, 'edit'])->name('matches.stats.edit');
        Route::put('/matches/{match}/stats', [MatchStatsController::class, 'update'])->name('matches.stats.update');
    });

    // Manager routes
    Route::middleware('role:manager')->prefix('manager')->name('manager.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'manager'])->name('dashboard');
        Route::resource('teams', TeamController::class)->only(['index', 'show', 'edit', 'update']);
        Route::resource('players', PlayerController::class);
        Route::post('/players/{player}/create-account', [PlayerController::class, 'createAccount'])->name('players.create-account');
        Route::resource('trainings', TrainingController::class)->only(['index', 'show']);
        Route::resource('matches', MatchController::class)->only(['index', 'show']);
    });

    // Player routes
    Route::middleware('role:player')->prefix('player')->name('player.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'player'])->name('dashboard');
    });
});
