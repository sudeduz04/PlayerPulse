<?php

namespace App\Services;

use App\Models\DevelopmentReports;
use App\Models\Players;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DevelopmentReportService
{
    public function listByPlayer(int $playerId, User $user): LengthAwarePaginator
    {
        $player = $this->authorizedPlayer($playerId, $user);

        return DevelopmentReports::with('creator')
            ->where('player_id', $player->id)
            ->latest('report_date')
            ->paginate(15);
    }

    public function show(int $id, User $user): DevelopmentReports
    {
        $report = DevelopmentReports::with(['player.team', 'creator'])->findOrFail($id);
        $this->authorizePlayerAccess($user, $report->player);

        return $report;
    }

    public function create(array $data, User $user): DevelopmentReports
    {
        $this->authorizedPlayer($data['player_id'], $user);
        $data['created_by'] = $user->id;

        return DevelopmentReports::create($data)->load('creator');
    }

    public function update(int $id, array $data, User $user): DevelopmentReports
    {
        $report = DevelopmentReports::findOrFail($id);
        $this->authorizedPlayer($report->player_id, $user);
        $report->update($data);

        return $report->fresh(['player', 'creator']);
    }

    public function delete(int $id, User $user): void
    {
        $report = DevelopmentReports::findOrFail($id);
        $this->authorizedPlayer($report->player_id, $user);
        $report->delete();
    }

    private function authorizedPlayer(int $playerId, User $user): Players
    {
        $player = Players::with('team')->findOrFail($playerId);
        $this->authorizePlayerAccess($user, $player);

        return $player;
    }

    private function authorizePlayerAccess(User $user, Players $player): void
    {
        if ($user->isRole('coach') || $user->isRole('manager')) {
            if (! $user->getTeamIds()->contains($player->team_id)) {
                abort(403, 'Bu oyuncuya erişim yetkiniz yok.');
            }
        }
    }
}
