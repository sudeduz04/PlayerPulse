<?php

namespace App\Http\Controllers\Api;

use App\Models\Players;
use App\Models\Teams;
use App\Models\User;
use App\Services\InjuryService;
use App\Services\MatchStatsService;
use App\Services\PhysicalMeasurementService;
use App\Services\TrainingPerformanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    public function __construct(
        private TrainingPerformanceService $performanceService,
        private MatchStatsService $matchStatsService,
        private InjuryService $injuryService,
        private PhysicalMeasurementService $measurementService,
    ) {}

    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = match ($user->role) {
            User::ROLE_SUPER_ADMIN => $this->superAdminData(),
            User::ROLE_MANAGER => $this->managerData($user),
            User::ROLE_COACH => $this->coachData($user),
            User::ROLE_PLAYER => $this->playerData($user),
            default => [],
        };

        return $this->sendResponse($data, 'Dashboard retrieved successfully.');
    }

    private function superAdminData(): array
    {
        return [
            'total_teams' => Teams::count(),
            'total_players' => Players::count(),
            'total_users' => User::count(),
            'users_by_role' => User::selectRaw('role, count(*) as count')->groupBy('role')->pluck('count', 'role'),
            'active_players' => Players::where('status', 'active')->count(),
            'injured_players' => Players::where('status', 'injured')->count(),
            'inactive_players' => Players::where('status', 'inactive')->count(),
            'players_with_accounts' => Players::whereNotNull('user_id')->count(),
            'recent_users' => User::latest()->limit(5)->get(),
            'teams' => Teams::withCount(['players', 'coaches'])->latest()->limit(5)->get(),
            'recent_players' => Players::with(['team', 'position'])->latest()->limit(5)->get(),
            'team_player_counts' => Teams::withCount('players')->orderByDesc('players_count')->limit(8)->get(),
        ];
    }

    private function managerData(User $user): array
    {
        $teamIds = $user->getTeamIds();

        return [
            'total_teams' => Teams::whereIn('id', $teamIds)->count(),
            'total_players' => Players::whereIn('team_id', $teamIds)->count(),
            'total_coaches' => User::where('role', User::ROLE_COACH)
                ->whereHas('teams', fn ($q) => $q->whereIn('teams.id', $teamIds))
                ->count(),
            'active_players' => Players::whereIn('team_id', $teamIds)->where('status', 'active')->count(),
            'injured_players' => Players::whereIn('team_id', $teamIds)->where('status', 'injured')->count(),
            'inactive_players' => Players::whereIn('team_id', $teamIds)->where('status', 'inactive')->count(),
            'teams' => Teams::whereIn('id', $teamIds)->withCount(['players', 'coaches'])->latest()->limit(5)->get(),
            'recent_players' => Players::with(['team', 'position'])->whereIn('team_id', $teamIds)->latest()->limit(5)->get(),
            'team_player_counts' => Teams::whereIn('id', $teamIds)->withCount('players')->orderByDesc('players_count')->limit(8)->get(),
        ];
    }

    private function coachData(User $user): array
    {
        $teamIds = $user->getTeamIds();

        return [
            'my_teams' => Teams::whereIn('id', $teamIds)->with('players')->withCount('players')->get(),
            'total_players' => Players::whereIn('team_id', $teamIds)->count(),
            'active_players' => Players::whereIn('team_id', $teamIds)->where('status', 'active')->count(),
            'injured_players' => Players::whereIn('team_id', $teamIds)->where('status', 'injured')->count(),
            'recent_players' => Players::with(['team', 'position'])->whereIn('team_id', $teamIds)->latest()->limit(5)->get(),
        ];
    }

    private function playerData(User $user): array
    {
        $player = Players::with(['team', 'position'])->where('user_id', $user->id)->first();

        if (! $player) {
            return [
                'player' => null,
                'team_players' => [],
                'training_summary' => $this->emptyTrainingSummary(),
                'recent_training_performances' => [],
                'match_summary' => $this->emptyMatchSummary(),
                'recent_match_stats' => [],
                'injury_summary' => $this->emptyInjurySummary(),
                'measurement_summary' => $this->emptyMeasurementSummary(),
            ];
        }

        return [
            'player' => $player,
            'team_players' => Players::with('position')
                ->where('team_id', $player->team_id)
                ->where('id', '!=', $player->id)
                ->limit(5)
                ->get(),
            'training_summary' => $this->performanceService->summaryForPlayer($user),
            'recent_training_performances' => $this->performanceService->recentHistoryForPlayer($user, 5),
            'match_summary' => $this->matchStatsService->summaryForPlayer($user),
            'recent_match_stats' => $this->matchStatsService->recentHistoryForPlayer($user, 5),
            'injury_summary' => $this->injuryService->summaryForPlayer($user),
            'measurement_summary' => $this->measurementService->summaryForPlayer($user),
        ];
    }

    private function emptyTrainingSummary(): array
    {
        return [
            'total_trainings' => 0,
            'attended' => 0,
            'absent' => 0,
            'excused' => 0,
            'attendance_rate' => 0.0,
            'average_score' => null,
        ];
    }

    private function emptyMatchSummary(): array
    {
        return [
            'total_matches' => 0,
            'starts' => 0,
            'minutes' => 0,
            'goals' => 0,
            'assists' => 0,
            'average_rating' => null,
            'average_pass_accuracy' => null,
            'yellow_cards' => 0,
            'red_cards' => 0,
        ];
    }

    private function emptyInjurySummary(): array
    {
        return [
            'total_injuries' => 0,
            'ongoing' => 0,
            'recovered' => 0,
            'latest_injury' => null,
        ];
    }

    private function emptyMeasurementSummary(): array
    {
        return [
            'total_measurements' => 0,
            'latest_measurement' => null,
            'latest_height' => null,
            'latest_weight' => null,
            'latest_body_fat_percentage' => null,
            'best_sprint_time' => null,
            'average_endurance' => null,
            'average_strength' => null,
        ];
    }
}
