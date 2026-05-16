<?php

namespace App\Services;

use App\Models\PhysicalMeasurements;
use App\Models\Players;
use App\Models\User;
use App\Services\Authorization\TeamAccess;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class PhysicalMeasurementService
{
    public function __construct(private TeamAccess $teamAccess) {}

    public function list(array $filters, User $user): LengthAwarePaginator
    {
        $query = PhysicalMeasurements::with(['player.team', 'player.position']);

        $this->scopeForUser($query, $user);
        $this->applyFilters($query, $filters);

        return $query
            ->latest('measurement_date')
            ->paginate($this->perPage($filters));
    }

    public function listByPlayer(int $playerId, User $user): LengthAwarePaginator
    {
        $player = $this->authorizedPlayer($playerId, $user);

        return PhysicalMeasurements::where('player_id', $player->id)
            ->latest('measurement_date')
            ->paginate(15);
    }

    public function listForCurrentPlayer(User $user, array $filters = []): LengthAwarePaginator
    {
        abort_unless($user->isRole(User::ROLE_PLAYER), 403, 'Bu işlem yalnızca oyuncular içindir.');
        abort_unless($user->player, 404, 'Oyuncu profili bulunamadı.');

        return $this->list(array_merge($filters, ['player_id' => $user->player->id]), $user);
    }

    public function create(array $data, User $user): PhysicalMeasurements
    {
        abort_if($user->isRole(User::ROLE_PLAYER), 403, 'Oyuncular fiziksel ölçüm kaydı yönetemez.');

        $this->authorizedPlayer((int) $data['player_id'], $user);

        return PhysicalMeasurements::create($data)->load(['player.team', 'player.position']);
    }

    public function update(int $id, array $data, User $user): PhysicalMeasurements
    {
        abort_if($user->isRole(User::ROLE_PLAYER), 403, 'Oyuncular fiziksel ölçüm kaydı yönetemez.');

        $measurement = PhysicalMeasurements::findOrFail($id);
        $this->authorizedPlayer($measurement->player_id, $user);
        $measurement->update($data);

        return $measurement->fresh(['player.team', 'player.position']);
    }

    public function delete(int $id, User $user): void
    {
        abort_if($user->isRole(User::ROLE_PLAYER), 403, 'Oyuncular fiziksel ölçüm kaydı yönetemez.');

        $measurement = PhysicalMeasurements::findOrFail($id);
        $this->authorizedPlayer($measurement->player_id, $user);
        $measurement->delete();
    }

    public function summaryForPlayer(User $user, array $filters = []): array
    {
        abort_unless($user->isRole(User::ROLE_PLAYER), 403, 'Bu işlem yalnızca oyuncular içindir.');

        if (! $user->player) {
            return $this->emptySummary();
        }

        return $this->summary(array_merge($filters, ['player_id' => $user->player->id]), $user);
    }

    public function summary(array $filters, User $user): array
    {
        $query = PhysicalMeasurements::query();
        $this->scopeForUser($query, $user);
        $this->applyFilters($query, $filters);

        $latest = (clone $query)->latest('measurement_date')->first();
        $bestSprint = (clone $query)->whereNotNull('sprint_time')->min('sprint_time');
        $averageEndurance = (clone $query)->whereNotNull('endurance_level')->avg('endurance_level');
        $averageStrength = (clone $query)->whereNotNull('strength_score')->avg('strength_score');

        return [
            'total_measurements' => (clone $query)->count(),
            'latest_measurement' => $latest,
            'latest_height' => $latest?->height !== null ? (float) $latest->height : null,
            'latest_weight' => $latest?->weight !== null ? (float) $latest->weight : null,
            'latest_body_fat_percentage' => $latest?->body_fat_percentage !== null ? (float) $latest->body_fat_percentage : null,
            'best_sprint_time' => $bestSprint !== null ? (float) $bestSprint : null,
            'average_endurance' => $averageEndurance !== null ? round((float) $averageEndurance, 2) : null,
            'average_strength' => $averageStrength !== null ? round((float) $averageStrength, 2) : null,
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

        if (! empty($filters['date_from'])) {
            $query->where('measurement_date', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->where('measurement_date', '<=', $filters['date_to']);
        }
    }

    private function emptySummary(): array
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

    private function perPage(array $filters): int
    {
        return max(1, min((int) ($filters['per_page'] ?? 15), 100));
    }
}
