<?php

namespace App\Services;

use App\Models\Injuries;
use App\Models\Players;
use App\Models\User;
use App\Services\Authorization\TeamAccess;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class InjuryService
{
    public function __construct(private TeamAccess $teamAccess) {}

    public function list(array $filters, User $user): LengthAwarePaginator
    {
        $query = Injuries::with(['player.team', 'player.position']);

        $this->scopeForUser($query, $user);
        $this->applyFilters($query, $filters);

        return $query
            ->latest('start_date')
            ->paginate($this->perPage($filters));
    }

    public function listByPlayer(int $playerId, User $user): LengthAwarePaginator
    {
        $player = $this->authorizedPlayer($playerId, $user);

        return Injuries::where('player_id', $player->id)
            ->latest('start_date')
            ->paginate(15);
    }

    public function listForCurrentPlayer(User $user, array $filters = []): LengthAwarePaginator
    {
        abort_unless($user->isRole(User::ROLE_PLAYER), 403, 'Bu işlem yalnızca oyuncular içindir.');
        abort_unless($user->player, 404, 'Oyuncu profili bulunamadı.');

        return $this->list(array_merge($filters, ['player_id' => $user->player->id]), $user);
    }

    public function show(int $id, User $user): Injuries
    {
        $injury = Injuries::with('player.team')->findOrFail($id);
        $this->teamAccess->assertPlayer($user, $injury->player);

        return $injury;
    }

    public function create(array $data, User $user): Injuries
    {
        abort_if($user->isRole(User::ROLE_PLAYER), 403, 'Oyuncular sakatlık kaydı yönetemez.');

        $this->authorizedPlayer((int) $data['player_id'], $user);

        return Injuries::create($data)->load(['player.team', 'player.position']);
    }

    public function update(int $id, array $data, User $user): Injuries
    {
        abort_if($user->isRole(User::ROLE_PLAYER), 403, 'Oyuncular sakatlık kaydı yönetemez.');

        $injury = Injuries::findOrFail($id);
        $this->authorizedPlayer($injury->player_id, $user);
        $injury->update($data);

        return $injury->fresh(['player.team', 'player.position']);
    }

    public function delete(int $id, User $user): void
    {
        abort_if($user->isRole(User::ROLE_PLAYER), 403, 'Oyuncular sakatlık kaydı yönetemez.');

        $injury = Injuries::findOrFail($id);
        $this->authorizedPlayer($injury->player_id, $user);
        $injury->delete();
    }

    public function summaryForPlayer(User $user): array
    {
        abort_unless($user->isRole(User::ROLE_PLAYER), 403, 'Bu işlem yalnızca oyuncular içindir.');

        if (! $user->player) {
            return $this->emptySummary();
        }

        return $this->summary(['player_id' => $user->player->id], $user);
    }

    public function summary(array $filters, User $user): array
    {
        $query = Injuries::query();
        $this->scopeForUser($query, $user);
        $this->applyFilters($query, $filters);

        return [
            'total_injuries' => (clone $query)->count(),
            'ongoing' => (clone $query)->where('status', 'ongoing')->count(),
            'recovered' => (clone $query)->where('status', 'recovered')->count(),
            'latest_injury' => (clone $query)->latest('start_date')->first(),
        ];
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
        if (! empty($filters['player_id'])) {
            $query->where('player_id', $filters['player_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['date_from'])) {
            $query->where('start_date', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->where('start_date', '<=', $filters['date_to']);
        }
    }

    private function emptySummary(): array
    {
        return [
            'total_injuries' => 0,
            'ongoing' => 0,
            'recovered' => 0,
            'latest_injury' => null,
        ];
    }

    private function perPage(array $filters): int
    {
        return max(1, min((int) ($filters['per_page'] ?? 15), 100));
    }
}
