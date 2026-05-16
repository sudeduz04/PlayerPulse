<?php

namespace Tests\Unit;

use App\Models\Players;
use App\Models\Positions;
use App\Models\Teams;
use App\Models\User;
use App\Services\Authorization\TeamAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_access_any_team(): void
    {
        $team = Teams::create($this->teamPayload());
        $user = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $this->assertTrue(app(TeamAccess::class)->canAccessTeam($user, $team->id));
    }

    public function test_manager_access_is_limited_to_assigned_teams(): void
    {
        $assigned = Teams::create($this->teamPayload(['name' => 'Assigned']));
        $unassigned = Teams::create($this->teamPayload(['name' => 'Unassigned']));
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $assigned->staff()->attach($manager->id);

        $access = app(TeamAccess::class);

        $this->assertTrue($access->canAccessTeam($manager, $assigned->id));
        $this->assertFalse($access->canAccessTeam($manager, $unassigned->id));
    }

    public function test_player_access_is_limited_to_own_player_record(): void
    {
        $team = Teams::create($this->teamPayload());
        $position = Positions::create(['name' => 'Goalkeeper', 'code' => 'GK']);
        $user = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $ownPlayer = Players::create($this->playerPayload($team->id, $position->id, ['user_id' => $user->id]));
        $otherPlayer = Players::create($this->playerPayload($team->id, $position->id, [
            'first_name' => 'Other',
            'jersey_number' => 2,
        ]));

        $access = app(TeamAccess::class);

        $this->assertTrue($access->canAccessPlayer($user, $ownPlayer));
        $this->assertFalse($access->canAccessPlayer($user, $otherPlayer));
    }

    private function teamPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Unit Team',
            'age_category' => 'U16',
            'season' => '2025-2026',
            'description' => null,
        ], $overrides);
    }

    private function playerPayload(int $teamId, int $positionId, array $overrides = []): array
    {
        return array_merge([
            'team_id' => $teamId,
            'position_id' => $positionId,
            'first_name' => 'Can',
            'last_name' => 'Yilmaz',
            'birth_date' => '2009-02-01',
            'jersey_number' => 1,
            'dominant_foot' => 'right',
            'status' => 'active',
        ], $overrides);
    }
}
