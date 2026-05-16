<?php

namespace App\Providers;

use App\Models\Matches;
use App\Models\Players;
use App\Models\Teams;
use App\Models\Trainings;
use App\Policies\MatchPolicy;
use App\Policies\PlayerPolicy;
use App\Policies\TeamPolicy;
use App\Policies\TrainingPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Teams::class, TeamPolicy::class);
        Gate::policy(Players::class, PlayerPolicy::class);
        Gate::policy(Trainings::class, TrainingPolicy::class);
        Gate::policy(Matches::class, MatchPolicy::class);
    }
}
