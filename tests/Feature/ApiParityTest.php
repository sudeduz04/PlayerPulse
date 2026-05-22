<?php

namespace Tests\Feature;

use App\Models\AiRecommendations;
use App\Models\Leagues;
use App\Models\Lineups;
use App\Models\Players;
use App\Models\Positions;
use App\Models\Teams;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiParityTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_crud_leagues_through_api(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $team = $this->team('Alfa');

        $created = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/leagues', [
                'name' => 'Test Lig',
                'season' => '2026-2027',
                'team_ids' => [$team->id],
            ])
            ->assertCreated()
            ->assertJsonPath('success', true);

        $leagueId = $created->json('data.id');

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/leagues')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->actingAs($admin, 'sanctum')
            ->getJson("/api/leagues/{$leagueId}")
            ->assertOk()
            ->assertJsonPath('data.id', $leagueId);

        $this->actingAs($admin, 'sanctum')
            ->putJson("/api/leagues/{$leagueId}", [
                'name' => 'Test Lig 2',
                'season' => '2026-2027',
                'team_ids' => [$team->id],
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Test Lig 2');

        $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/leagues/{$leagueId}")
            ->assertOk();

        $this->assertDatabaseMissing('leagues', ['id' => $leagueId, 'deleted_at' => null]);
    }

    public function test_lineup_status_endpoint_returns_label(): void
    {
        $coach = User::factory()->create(['role' => User::ROLE_COACH]);
        $team = $this->team('Alfa');
        $team->staff()->attach($coach->id);
        $match = \App\Models\Matches::create([
            'team_id' => $team->id,
            'opponent_team' => 'Rakip',
            'match_date' => '2026-06-01',
            'match_type' => 'league',
            'location' => 'home',
        ]);

        $lineup = Lineups::create([
            'match_id' => $match->id,
            'created_by' => $coach->id,
            'formation' => '4-4-2',
            'is_ai_generated' => true,
            'status' => 'queued',
        ]);

        $this->actingAs($coach, 'sanctum')
            ->getJson("/api/lineups/{$lineup->id}/status")
            ->assertOk()
            ->assertJsonPath('data.status', 'queued')
            ->assertJsonPath('data.status_label', 'Sıraya alındı')
            ->assertJsonPath('data.is_ai_generated', true);
    }

    public function test_analysis_status_endpoint_returns_label(): void
    {
        $coach = User::factory()->create(['role' => User::ROLE_COACH]);
        $team = $this->team('Alfa');
        $team->staff()->attach($coach->id);
        $position = Positions::create(['name' => 'FW', 'code' => 'FW1']);
        $player = Players::create([
            'team_id' => $team->id,
            'position_id' => $position->id,
            'first_name' => 'A',
            'last_name' => 'B',
            'jersey_number' => 1,
            'birth_date' => '2005-01-01',
            'dominant_foot' => 'right',
            'status' => 'active',
        ]);

        $analysis = AiRecommendations::create([
            'player_id' => $player->id,
            'recommendation_type' => 'player_analysis',
            'status' => 'queued',
        ]);

        $this->actingAs($coach, 'sanctum')
            ->getJson("/api/analysis/{$analysis->id}/status")
            ->assertOk()
            ->assertJsonPath('data.status', 'queued')
            ->assertJsonPath('data.status_label', 'Sıraya alındı');
    }

    public function test_team_staff_alias_endpoint_works(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $coach = User::factory()->create(['role' => User::ROLE_COACH]);
        $team = $this->team('Alfa');

        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/teams/{$team->id}/staff", ['user_id' => $coach->id])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => $coach->id,
        ]);

        $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/teams/{$team->id}/staff/{$coach->id}")
            ->assertOk();
    }

    public function test_non_super_admin_cannot_create_league_through_api(): void
    {
        $coach = User::factory()->create(['role' => User::ROLE_COACH]);

        $this->actingAs($coach, 'sanctum')
            ->postJson('/api/leagues', [
                'name' => 'X',
                'season' => '2026-2027',
            ])
            ->assertForbidden();
    }

    private function team(string $name): Teams
    {
        return Teams::create([
            'name' => $name,
            'age_category' => 'U19',
            'season' => '2026-2027',
            'description' => null,
        ]);
    }
}
