<?php

namespace App\Services;

use App\Models\DevelopmentReports;
use App\Models\Players;
use App\Models\User;
use App\Services\Authorization\TeamAccess;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class DevelopmentReportService
{
    public function __construct(private TeamAccess $teamAccess) {}

    public function list(array $filters, User $user): LengthAwarePaginator
    {
        $query = DevelopmentReports::with(['player.team', 'player.position', 'creator']);

        $this->scopeForUser($query, $user);
        $this->applyFilters($query, $filters);

        $perPage = max(1, min((int) ($filters['per_page'] ?? 15), 100));

        return $query->latest('report_date')->paginate($perPage);
    }

    public function listByPlayer(int $playerId, User $user): LengthAwarePaginator
    {
        $player = $this->authorizedPlayer($playerId, $user);

        return DevelopmentReports::with('creator')
            ->where('player_id', $player->id)
            ->latest('report_date')
            ->paginate(15);
    }

    public function listForCurrentPlayer(User $user, array $filters = []): LengthAwarePaginator
    {
        abort_unless($user->isRole(User::ROLE_PLAYER), 403, 'Bu işlem yalnızca oyuncular içindir.');
        abort_unless($user->player, 404, 'Oyuncu profili bulunamadı.');

        return $this->list(array_merge($filters, ['player_id' => $user->player->id]), $user);
    }

    public function show(int $id, User $user): DevelopmentReports
    {
        $report = DevelopmentReports::with(['player.team', 'player.position', 'creator'])->findOrFail($id);
        $this->teamAccess->assertPlayer($user, $report->player);

        return $report;
    }

    public function create(array $data, User $user): DevelopmentReports
    {
        abort_unless(
            $user->isSuperAdmin() || $user->isRole(User::ROLE_COACH),
            403,
            'Gelişim raporu oluşturmak için antrenör veya süper yönetici yetkisi gerekir.'
        );

        $player = $this->authorizedPlayer((int) $data['player_id'], $user);
        $data['player_id'] = $player->id;
        $data['created_by'] = $user->id;

        return DevelopmentReports::create($data)->load(['player.team', 'player.position', 'creator']);
    }

    public function update(int $id, array $data, User $user): DevelopmentReports
    {
        abort_unless(
            $user->isSuperAdmin() || $user->isRole(User::ROLE_COACH),
            403,
            'Gelişim raporu güncellemek için antrenör veya süper yönetici yetkisi gerekir.'
        );

        $report = DevelopmentReports::findOrFail($id);
        $this->authorizedPlayer($report->player_id, $user);
        $report->update($data);

        return $report->fresh(['player.team', 'player.position', 'creator']);
    }

    public function delete(int $id, User $user): void
    {
        abort_unless(
            $user->isSuperAdmin() || $user->isRole(User::ROLE_COACH),
            403,
            'Gelişim raporu silmek için antrenör veya süper yönetici yetkisi gerekir.'
        );

        $report = DevelopmentReports::findOrFail($id);
        $this->authorizedPlayer($report->player_id, $user);
        $report->delete();
    }

    public function summary(array $filters, User $user): array
    {
        $query = DevelopmentReports::query();
        $this->scopeForUser($query, $user);
        $this->applyFilters($query, $filters);

        $total = (clone $query)->count();
        $averageOverall = (clone $query)->whereNotNull('overall_score')->avg('overall_score');
        $averageTechnical = (clone $query)->whereNotNull('technical_development')->avg('technical_development');
        $averagePhysical = (clone $query)->whereNotNull('physical_development')->avg('physical_development');
        $averageTactical = (clone $query)->whereNotNull('tactical_development')->avg('tactical_development');
        $averageMental = (clone $query)->whereNotNull('mental_development')->avg('mental_development');

        return [
            'total_reports' => $total,
            'average_overall' => $averageOverall !== null ? round((float) $averageOverall, 2) : null,
            'average_technical' => $averageTechnical !== null ? round((float) $averageTechnical, 2) : null,
            'average_physical' => $averagePhysical !== null ? round((float) $averagePhysical, 2) : null,
            'average_tactical' => $averageTactical !== null ? round((float) $averageTactical, 2) : null,
            'average_mental' => $averageMental !== null ? round((float) $averageMental, 2) : null,
        ];
    }

    public function playersForEvaluation(User $user)
    {
        $query = Players::with(['team', 'position'])->where('status', 'active');

        if ($user->isRole(User::ROLE_COACH) || $user->isRole(User::ROLE_MANAGER)) {
            $query->whereIn('team_id', $user->getTeamIds());
        }

        if ($user->isRole(User::ROLE_PLAYER)) {
            $query->where('user_id', $user->id);
        }

        return $query
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
    }

    private function authorizedPlayer(int $playerId, User $user): Players
    {
        $player = Players::with('team')->findOrFail($playerId);
        $this->teamAccess->assertPlayer($user, $player);

        return $player;
    }

    private function scopeForUser(Builder $query, User $user): void
    {
        if ($user->isSuperAdmin()) {
            return;
        }

        if ($user->isRole(User::ROLE_COACH) || $user->isRole(User::ROLE_MANAGER)) {
            $query->whereHas('player', fn (Builder $q) => $q->whereIn('team_id', $user->getTeamIds()));

            return;
        }

        if ($user->isRole(User::ROLE_PLAYER)) {
            $query->where('player_id', $user->player?->id);

            return;
        }

        abort(403);
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        if (! empty($filters['team_id'])) {
            $query->whereHas('player', fn (Builder $q) => $q->where('team_id', $filters['team_id']));
        }

        if (! empty($filters['player_id'])) {
            $query->where('player_id', $filters['player_id']);
        }

        if (! empty($filters['date_from'])) {
            $query->where('report_date', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->where('report_date', '<=', $filters['date_to']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('player', function (Builder $q) use ($search) {
                $q->where('first_name', 'like', '%'.$search.'%')
                    ->orWhere('last_name', 'like', '%'.$search.'%');
            });
        }
    }
}
