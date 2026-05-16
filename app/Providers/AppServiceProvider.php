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
use App\Services\Ai\AiProvider;
use App\Services\Ai\GeminiProvider;
use App\Services\Ai\NullAiProvider;
use App\Services\Ai\OpenAiProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AiProvider::class, function () {
            $explicit = config('services.ai.provider');
            $openAiKey = (string) config('services.ai.openai.key');
            $geminiKey = (string) config('services.ai.gemini.key');

            $provider = $explicit
                ?: ($openAiKey !== '' ? 'openai'
                    : ($geminiKey !== '' ? 'gemini' : null));

            return match ($provider) {
                'openai' => new OpenAiProvider($openAiKey, (string) config('services.ai.openai.model')),
                'gemini' => new GeminiProvider($geminiKey, (string) config('services.ai.gemini.model')),
                default => new NullAiProvider,
            };
        });
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
