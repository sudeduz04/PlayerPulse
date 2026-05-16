<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Players;
use App\Models\Teams;
use App\Models\User;
use App\Services\InjuryService;
use App\Services\MatchStatsService;
use App\Services\PhysicalMeasurementService;
use App\Services\TrainingPerformanceService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private TrainingPerformanceService $performanceService,
        private MatchStatsService $matchStatsService,
        private InjuryService $injuryService,
        private PhysicalMeasurementService $measurementService,
    ) {}

    public function superAdmin(Request $request)
    {
        $totalTeams = Teams::count();
        $totalPlayers = Players::count();
        $totalUsers = User::count();
        $usersByRole = User::selectRaw('role, count(*) as count')->groupBy('role')->pluck('count', 'role');
        $activePlayers = Players::where('status', 'active')->count();
        $injuredPlayers = Players::where('status', 'injured')->count();
        $inactivePlayers = Players::where('status', 'inactive')->count();
        $playersWithAccounts = Players::whereNotNull('user_id')->count();

        $recentUsers = User::latest()->limit(5)->get();
        $teams = Teams::withCount(['players', 'coaches'])->latest()->limit(5)->get();
        $recentPlayers = Players::with(['team', 'position'])->latest()->limit(5)->get();

        $teamPlayerCounts = Teams::withCount('players')
            ->orderByDesc('players_count')
            ->limit(8)
            ->get();

        return view('super_admin.dashboard', compact(
            'totalTeams',
            'totalPlayers',
            'totalUsers',
            'usersByRole',
            'activePlayers',
            'injuredPlayers',
            'inactivePlayers',
            'playersWithAccounts',
            'recentUsers',
            'teams',
            'recentPlayers',
            'teamPlayerCounts',
        ));
    }

    public function manager(Request $request)
    {
        $user = $request->user();
        $teamIds = $user->getTeamIds();

        $totalTeams = Teams::whereIn('id', $teamIds)->count();
        $totalPlayers = Players::whereIn('team_id', $teamIds)->count();
        $totalCoaches = User::where('role', 'coach')
            ->whereHas('teams', fn ($q) => $q->whereIn('teams.id', $teamIds))
            ->count();
        $activePlayers = Players::whereIn('team_id', $teamIds)->where('status', 'active')->count();
        $injuredPlayers = Players::whereIn('team_id', $teamIds)->where('status', 'injured')->count();
        $inactivePlayers = Players::whereIn('team_id', $teamIds)->where('status', 'inactive')->count();

        $teams = Teams::whereIn('id', $teamIds)->withCount(['players', 'coaches'])->latest()->limit(5)->get();

        $recentPlayers = Players::with(['team', 'position'])
            ->whereIn('team_id', $teamIds)
            ->latest()
            ->limit(5)
            ->get();

        $teamPlayerCounts = Teams::whereIn('id', $teamIds)
            ->withCount('players')
            ->orderByDesc('players_count')
            ->limit(8)
            ->get();

        return view('manager.dashboard', compact(
            'totalTeams',
            'totalPlayers',
            'totalCoaches',
            'activePlayers',
            'injuredPlayers',
            'inactivePlayers',
            'teams',
            'recentPlayers',
            'teamPlayerCounts',
        ));
    }

    public function coach(Request $request)
    {
        $user = $request->user();
        $teamIds = $user->getTeamIds();

        $myTeams = Teams::whereIn('id', $teamIds)->with('players')->withCount('players')->get();
        $totalPlayers = Players::whereIn('team_id', $teamIds)->count();
        $activePlayers = Players::whereIn('team_id', $teamIds)->where('status', 'active')->count();
        $injuredPlayers = Players::whereIn('team_id', $teamIds)->where('status', 'injured')->count();

        $recentPlayers = Players::with(['team', 'position'])
            ->whereIn('team_id', $teamIds)
            ->latest()
            ->limit(5)
            ->get();

        return view('coach.dashboard', compact(
            'myTeams',
            'totalPlayers',
            'activePlayers',
            'injuredPlayers',
            'recentPlayers',
        ));
    }

    public function player(Request $request)
    {
        $user = $request->user();

        $player = Players::with(['team', 'position'])
            ->where('user_id', $user->id)
            ->first();

        $teamPlayers = $player
            ? Players::with('position')
                ->where('team_id', $player->team_id)
                ->where('id', '!=', $player->id)
                ->limit(5)
                ->get()
            : collect();

        $trainingSummary = $player
            ? $this->performanceService->summaryForPlayer($user)
            : [
                'total_trainings' => 0,
                'attended' => 0,
                'absent' => 0,
                'excused' => 0,
                'attendance_rate' => 0.0,
                'average_score' => null,
            ];

        $recentTrainingPerformances = $player
            ? $this->performanceService->recentHistoryForPlayer($user, 5)
            : collect();

        $matchSummary = $player
            ? $this->matchStatsService->summaryForPlayer($user)
            : [
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

        $recentMatchStats = $player
            ? $this->matchStatsService->recentHistoryForPlayer($user, 5)
            : collect();

        $injurySummary = $player
            ? $this->injuryService->summaryForPlayer($user)
            : [
                'total_injuries' => 0,
                'ongoing' => 0,
                'recovered' => 0,
                'latest_injury' => null,
            ];

        $measurementSummary = $player
            ? $this->measurementService->summaryForPlayer($user)
            : [
                'total_measurements' => 0,
                'latest_measurement' => null,
                'latest_height' => null,
                'latest_weight' => null,
                'latest_body_fat_percentage' => null,
                'best_sprint_time' => null,
                'average_endurance' => null,
                'average_strength' => null,
            ];

        return view('player.dashboard', compact(
            'player',
            'teamPlayers',
            'trainingSummary',
            'recentTrainingPerformances',
            'matchSummary',
            'recentMatchStats',
            'injurySummary',
            'measurementSummary',
        ));
    }
}
