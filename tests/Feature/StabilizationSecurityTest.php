<?php

namespace Tests\Feature;

use App\Models\Players;
use App\Models\Positions;
use App\Models\Teams;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StabilizationSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_registration_creates_player_accounts_only(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Ali',
            'surname' => 'Kaya',
            'email' => 'ali@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.role', User::ROLE_PLAYER)
            ->assertJsonStructure(['data' => ['token']]);

        $this->assertDatabaseHas('users', [
            'email' => 'ali@example.com',
            'role' => User::ROLE_PLAYER,
        ]);
    }

    public function test_public_registration_rejects_role_escalation_attempts(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Role',
            'surname' => 'Escalation',
            'email' => 'role@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => User::ROLE_MANAGER,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['role']);

        $this->assertDatabaseMissing('users', ['email' => 'role@example.com']);
    }

    public function test_login_logout_and_invalid_credentials_use_standard_api_responses(): void
    {
        $user = User::factory()->create([
            'email' => 'coach@example.com',
            'password' => 'password123',
            'role' => User::ROLE_COACH,
        ]);

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])
            ->assertUnauthorized()
            ->assertJson(['success' => false, 'message' => 'Invalid credentials']);

        $login = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $login
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['token']]);

        $this->withToken($login->json('data.token'))
            ->postJson('/api/logout')
            ->assertOk()
            ->assertJson(['success' => true]);
    }

    public function test_unauthenticated_api_access_is_standardized(): void
    {
        $this->getJson('/api/me')
            ->assertUnauthorized()
            ->assertJson(['success' => false, 'message' => 'Unauthenticated']);
    }

    public function test_super_admin_can_manage_teams_but_manager_cannot_create_or_delete_them(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $team = $this->team();

        $this->actingAs($superAdmin, 'sanctum')
            ->postJson('/api/teams', $this->teamPayload(['name' => 'U19']))
            ->assertCreated()
            ->assertJsonPath('success', true);

        $this->actingAs($manager, 'sanctum')
            ->postJson('/api/teams', $this->teamPayload(['name' => 'Forbidden']))
            ->assertForbidden()
            ->assertJsonPath('success', false);

        $this->actingAs($manager, 'sanctum')
            ->deleteJson("/api/teams/{$team->id}")
            ->assertForbidden()
            ->assertJsonPath('success', false);
    }

    public function test_manager_and_coach_are_limited_to_assigned_teams(): void
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $coach = User::factory()->create(['role' => User::ROLE_COACH]);
        $ownTeam = $this->team(['name' => 'Own']);
        $otherTeam = $this->team(['name' => 'Other']);

        $ownTeam->staff()->attach([$manager->id, $coach->id]);

        $this->actingAs($manager, 'sanctum')
            ->getJson("/api/teams/{$ownTeam->id}")
            ->assertOk();

        $this->actingAs($manager, 'sanctum')
            ->getJson("/api/teams/{$otherTeam->id}")
            ->assertForbidden()
            ->assertJsonPath('success', false);

        $this->actingAs($coach, 'sanctum')
            ->postJson('/api/players', $this->playerPayload($otherTeam->id, $this->position()->id))
            ->assertForbidden()
            ->assertJsonPath('success', false);
    }

    public function test_player_role_cannot_manage_protected_modules(): void
    {
        $playerUser = User::factory()->create(['role' => User::ROLE_PLAYER]);

        $this->actingAs($playerUser, 'sanctum')
            ->getJson('/api/players')
            ->assertForbidden()
            ->assertJsonPath('success', false);

        $this->actingAs($playerUser, 'sanctum')
            ->getJson('/api/trainings')
            ->assertForbidden()
            ->assertJsonPath('success', false);
    }

    public function test_training_crud_and_cross_team_performance_guard(): void
    {
        $coach = User::factory()->create(['role' => User::ROLE_COACH]);
        $team = $this->team(['name' => 'Training Team']);
        $otherTeam = $this->team(['name' => 'Other Training Team']);
        $position = $this->position();
        $team->staff()->attach($coach->id);

        $trainingResponse = $this->actingAs($coach, 'sanctum')
            ->postJson('/api/trainings', $this->trainingPayload($team->id))
            ->assertCreated()
            ->assertJsonPath('success', true);

        $trainingId = $trainingResponse->json('data.id');
        $ownPlayer = $this->player($team->id, $position->id);
        $otherPlayer = $this->player($otherTeam->id, $position->id, ['jersey_number' => 12]);

        $this->actingAs($coach, 'sanctum')
            ->postJson("/api/trainings/{$trainingId}/performances", [
                'player_id' => $ownPlayer->id,
                'attendance_status' => 'attended',
                'performance_score' => 8,
            ])
            ->assertCreated()
            ->assertJsonPath('success', true);

        $this->actingAs($coach, 'sanctum')
            ->postJson("/api/trainings/{$trainingId}/performances", [
                'player_id' => $otherPlayer->id,
                'attendance_status' => 'attended',
                'performance_score' => 8,
            ])
            ->assertUnprocessable()
            ->assertJsonPath('success', false);
    }

    public function test_match_crud_and_cross_team_stats_guard(): void
    {
        $coach = User::factory()->create(['role' => User::ROLE_COACH]);
        $team = $this->team(['name' => 'Match Team']);
        $otherTeam = $this->team(['name' => 'Other Match Team']);
        $position = $this->position();
        $team->staff()->attach($coach->id);

        $matchResponse = $this->actingAs($coach, 'sanctum')
            ->postJson('/api/matches', $this->matchPayload($team->id))
            ->assertCreated()
            ->assertJsonPath('success', true);

        $matchId = $matchResponse->json('data.id');
        $ownPlayer = $this->player($team->id, $position->id);
        $otherPlayer = $this->player($otherTeam->id, $position->id, ['jersey_number' => 18]);

        $this->actingAs($coach, 'sanctum')
            ->postJson("/api/matches/{$matchId}/stats", $this->matchStatsPayload($ownPlayer->id))
            ->assertCreated()
            ->assertJsonPath('success', true);

        $this->actingAs($coach, 'sanctum')
            ->postJson("/api/matches/{$matchId}/stats", $this->matchStatsPayload($otherPlayer->id))
            ->assertUnprocessable()
            ->assertJsonPath('success', false);
    }

    private function team(array $overrides = []): Teams
    {
        return Teams::create($this->teamPayload($overrides));
    }

    private function position(array $overrides = []): Positions
    {
        return Positions::create(array_merge([
            'name' => 'Forward',
            'code' => 'FW'.fake()->unique()->numberBetween(100, 999),
            'description' => null,
        ], $overrides));
    }

    private function player(int $teamId, int $positionId, array $overrides = []): Players
    {
        return Players::create(array_merge($this->playerPayload($teamId, $positionId), $overrides));
    }

    private function teamPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'U17',
            'age_category' => 'U17',
            'season' => '2025-2026',
            'description' => 'Test team',
        ], $overrides);
    }

    private function playerPayload(int $teamId, int $positionId): array
    {
        return [
            'team_id' => $teamId,
            'position_id' => $positionId,
            'first_name' => 'Emre',
            'last_name' => 'Demir',
            'birth_date' => '2008-01-01',
            'jersey_number' => 10,
            'height' => 178,
            'weight' => 70,
            'dominant_foot' => 'right',
            'nationality' => 'TR',
            'status' => 'active',
        ];
    }

    private function trainingPayload(int $teamId): array
    {
        return [
            'team_id' => $teamId,
            'title' => 'Finishing Training',
            'training_date' => now()->toDateString(),
            'training_type' => 'technical',
            'duration_minutes' => 90,
            'description' => 'Training test',
            'coach_note' => 'Good session',
        ];
    }

    private function matchPayload(int $teamId): array
    {
        return [
            'team_id' => $teamId,
            'opponent_team' => 'Rivals',
            'match_date' => now()->toDateString(),
            'match_type' => 'friendly',
            'location' => 'Home',
            'result' => null,
            'goals_for' => 2,
            'goals_against' => 1,
            'coach_note' => 'Solid match',
        ];
    }

    private function matchStatsPayload(int $playerId): array
    {
        return [
            'player_id' => $playerId,
            'minutes_played' => 90,
            'is_starting' => true,
            'goals' => 1,
            'assists' => 0,
            'shots' => 3,
            'successful_passes' => 22,
            'pass_accuracy' => 82,
            'tackles' => 2,
            'interceptions' => 1,
            'dribbles' => 4,
            'fouls' => 1,
            'yellow_cards' => 0,
            'red_cards' => 0,
            'match_rating' => 8,
        ];
    }
}
