<?php

namespace Tests\Feature;

use App\Models\Matches;
use App\Models\PlayerMatchStats;
use App\Models\Players;
use App\Models\Positions;
use App\Models\Teams;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchModulePhaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_player_can_view_only_own_match_history_with_summary(): void
    {
        [$playerUser, $player] = $this->playerUserWithProfile();
        [, $otherPlayer] = $this->playerUserWithProfile([
            'team' => ['name' => 'Other Team'],
            'player' => ['first_name' => 'Other', 'jersey_number' => 9],
        ]);

        $matchOne = $this->match($player->team_id, ['match_date' => '2026-04-01', 'opponent_team' => 'Alpha']);
        $matchTwo = $this->match($player->team_id, ['match_date' => '2026-04-10', 'opponent_team' => 'Beta']);
        $otherMatch = $this->match($otherPlayer->team_id, ['match_date' => '2026-04-12', 'opponent_team' => 'Hidden']);

        $this->stat($matchOne->id, $player->id, [
            'is_starting' => true,
            'minutes_played' => 90,
            'goals' => 1,
            'assists' => 0,
            'pass_accuracy' => 80,
            'match_rating' => 8,
        ]);
        $this->stat($matchTwo->id, $player->id, [
            'is_starting' => false,
            'minutes_played' => 30,
            'goals' => 0,
            'assists' => 1,
            'pass_accuracy' => 90,
            'match_rating' => 7,
        ]);
        $this->stat($otherMatch->id, $otherPlayer->id, ['match_rating' => 10]);

        $response = $this->actingAs($playerUser, 'sanctum')
            ->getJson('/api/my/matches');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.summary.total_matches', 2)
            ->assertJsonPath('data.summary.starts', 1)
            ->assertJsonPath('data.summary.minutes', 120)
            ->assertJsonPath('data.summary.goals', 1)
            ->assertJsonPath('data.summary.assists', 1)
            ->assertJsonPath('data.summary.average_rating', 7.5)
            ->assertJsonPath('data.summary.average_pass_accuracy', 85)
            ->assertJsonCount(2, 'data.stats.data');

        $opponents = collect($response->json('data.stats.data'))->pluck('match.opponent_team');
        $this->assertTrue($opponents->contains('Alpha'));
        $this->assertTrue($opponents->contains('Beta'));
        $this->assertFalse($opponents->contains('Hidden'));
    }

    public function test_player_match_history_filters_by_date_and_match_type(): void
    {
        [$playerUser, $player] = $this->playerUserWithProfile();

        $this->stat(
            $this->match($player->team_id, ['match_date' => '2026-05-01', 'match_type' => 'league'])->id,
            $player->id,
            ['match_rating' => 6]
        );
        $this->stat(
            $this->match($player->team_id, ['match_date' => '2026-05-20', 'match_type' => 'cup'])->id,
            $player->id,
            ['match_rating' => 9]
        );

        $this->actingAs($playerUser, 'sanctum')
            ->getJson('/api/my/matches?match_type=cup&date_from=2026-05-10')
            ->assertOk()
            ->assertJsonPath('data.summary.total_matches', 1)
            ->assertJsonPath('data.summary.average_rating', 9)
            ->assertJsonPath('data.stats.data.0.match.match_type', 'cup');
    }

    public function test_non_player_roles_cannot_use_player_match_history_endpoint(): void
    {
        $coach = User::factory()->create(['role' => User::ROLE_COACH]);

        $this->actingAs($coach, 'sanctum')
            ->getJson('/api/my/matches')
            ->assertForbidden()
            ->assertJsonPath('success', false);
    }

    public function test_coach_match_writes_are_limited_to_assigned_teams(): void
    {
        $coach = User::factory()->create(['role' => User::ROLE_COACH]);
        $ownTeam = $this->team(['name' => 'Own Match Team']);
        $otherTeam = $this->team(['name' => 'Other Match Team']);
        $ownTeam->staff()->attach($coach->id);

        $matchId = $this->actingAs($coach, 'sanctum')
            ->postJson('/api/matches', $this->matchPayload($ownTeam->id))
            ->assertCreated()
            ->json('data.id');

        $this->actingAs($coach, 'sanctum')
            ->patchJson("/api/matches/{$matchId}", array_merge(
                $this->matchPayload($ownTeam->id),
                ['opponent_team' => 'Updated Opponent']
            ))
            ->assertOk()
            ->assertJsonPath('data.opponent_team', 'Updated Opponent');

        $this->actingAs($coach, 'sanctum')
            ->deleteJson("/api/matches/{$matchId}")
            ->assertOk();

        $this->actingAs($coach, 'sanctum')
            ->postJson('/api/matches', $this->matchPayload($otherTeam->id))
            ->assertForbidden()
            ->assertJsonPath('success', false);
    }

    public function test_manager_can_read_assigned_matches_but_cannot_write(): void
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $team = $this->team();
        $team->staff()->attach($manager->id);
        $match = $this->match($team->id);

        $this->actingAs($manager, 'sanctum')
            ->getJson('/api/matches')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->actingAs($manager, 'sanctum')
            ->getJson("/api/matches/{$match->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $match->id);

        $this->actingAs($manager, 'sanctum')
            ->postJson('/api/matches', $this->matchPayload($team->id))
            ->assertForbidden()
            ->assertJsonPath('success', false);

        $this->actingAs($manager, 'sanctum')
            ->patchJson("/api/matches/{$match->id}", $this->matchPayload($team->id))
            ->assertForbidden()
            ->assertJsonPath('success', false);
    }

    public function test_super_admin_can_create_match_for_any_team(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $team = $this->team();

        $this->actingAs($superAdmin, 'sanctum')
            ->postJson('/api/matches', $this->matchPayload($team->id))
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.team_id', $team->id);
    }

    public function test_player_match_history_web_page_renders_for_linked_player(): void
    {
        [$playerUser, $player] = $this->playerUserWithProfile();
        $match = $this->match($player->team_id, ['opponent_team' => 'Web Opponent']);
        $this->stat($match->id, $player->id, ['goals' => 2, 'match_rating' => 9]);

        $this->actingAs($playerUser)
            ->get('/player/matches')
            ->assertOk()
            ->assertSee('Performansım')
            ->assertSee('Web Opponent');
    }

    private function playerUserWithProfile(array $overrides = []): array
    {
        $user = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $team = $this->team($overrides['team'] ?? []);
        $position = $this->position();
        $player = $this->player($team->id, $position->id, array_merge([
            'user_id' => $user->id,
        ], $overrides['player'] ?? []));

        return [$user, $player];
    }

    private function team(array $overrides = []): Teams
    {
        return Teams::create(array_merge([
            'name' => 'Match Phase Team',
            'age_category' => 'U18',
            'season' => '2025-2026',
            'description' => null,
        ], $overrides));
    }

    private function position(): Positions
    {
        return Positions::create([
            'name' => 'Striker',
            'code' => 'ST'.fake()->unique()->numberBetween(100, 999),
            'description' => null,
        ]);
    }

    private function player(int $teamId, int $positionId, array $overrides = []): Players
    {
        return Players::create(array_merge([
            'team_id' => $teamId,
            'position_id' => $positionId,
            'first_name' => 'Kerem',
            'last_name' => 'Yilmaz',
            'birth_date' => '2008-08-08',
            'jersey_number' => 17,
            'height' => 180,
            'weight' => 72,
            'dominant_foot' => 'right',
            'nationality' => 'TR',
            'status' => 'active',
        ], $overrides));
    }

    private function match(int $teamId, array $overrides = []): Matches
    {
        return Matches::create(array_merge($this->matchPayload($teamId), $overrides));
    }

    private function stat(int $matchId, int $playerId, array $overrides = []): PlayerMatchStats
    {
        return PlayerMatchStats::create(array_merge([
            'match_id' => $matchId,
            'player_id' => $playerId,
            'minutes_played' => 90,
            'is_starting' => true,
            'goals' => 0,
            'assists' => 0,
            'shots' => 2,
            'successful_passes' => 20,
            'pass_accuracy' => 80,
            'tackles' => 1,
            'interceptions' => 1,
            'dribbles' => 1,
            'fouls' => 0,
            'yellow_cards' => 0,
            'red_cards' => 0,
            'match_rating' => 7,
        ], $overrides));
    }

    private function matchPayload(int $teamId): array
    {
        return [
            'team_id' => $teamId,
            'opponent_team' => 'Phase Opponent',
            'match_date' => '2026-05-01',
            'match_type' => 'league',
            'location' => 'Home',
            'result' => 'win',
            'goals_for' => 2,
            'goals_against' => 1,
            'coach_note' => 'Good match',
        ];
    }
}
