<?php

namespace Tests\Feature;

use App\Models\Injuries;
use App\Models\PhysicalMeasurements;
use App\Models\Players;
use App\Models\Positions;
use App\Models\Teams;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthMetricsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_player_can_view_only_own_health_data_with_summaries(): void
    {
        [$playerUser, $player] = $this->playerUserWithProfile();
        [, $otherPlayer] = $this->playerUserWithProfile([
            'team' => ['name' => 'Other Health Team'],
            'player' => ['first_name' => 'Other', 'jersey_number' => 23],
        ]);

        $this->injury($player->id, ['status' => 'ongoing', 'injury_type' => 'Hamstring']);
        $this->injury($player->id, ['status' => 'recovered', 'injury_type' => 'Ankle', 'start_date' => '2026-02-01']);
        $this->injury($otherPlayer->id, ['status' => 'ongoing', 'injury_type' => 'Hidden']);

        $this->measurement($player->id, [
            'measurement_date' => '2026-03-01',
            'weight' => 72,
            'sprint_time' => 5.2,
            'endurance_level' => 7,
            'strength_score' => 8,
        ]);
        $this->measurement($player->id, [
            'measurement_date' => '2026-03-10',
            'weight' => 71,
            'sprint_time' => 5.0,
            'endurance_level' => 9,
            'strength_score' => 8,
        ]);
        $this->measurement($otherPlayer->id, ['weight' => 99]);

        $response = $this->actingAs($playerUser, 'sanctum')
            ->getJson('/api/my/health');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.injury_summary.total_injuries', 2)
            ->assertJsonPath('data.injury_summary.ongoing', 1)
            ->assertJsonPath('data.injury_summary.recovered', 1)
            ->assertJsonPath('data.measurement_summary.total_measurements', 2)
            ->assertJsonPath('data.measurement_summary.latest_weight', 71)
            ->assertJsonPath('data.measurement_summary.best_sprint_time', 5)
            ->assertJsonPath('data.measurement_summary.average_endurance', 8)
            ->assertJsonCount(2, 'data.injuries.data')
            ->assertJsonCount(2, 'data.measurements.data');

        $injuryTypes = collect($response->json('data.injuries.data'))->pluck('injury_type');
        $this->assertFalse($injuryTypes->contains('Hidden'));
    }

    public function test_player_health_filters_injuries_by_status_and_dates(): void
    {
        [$playerUser, $player] = $this->playerUserWithProfile();

        $this->injury($player->id, ['status' => 'recovered', 'start_date' => '2026-01-01']);
        $this->injury($player->id, ['status' => 'ongoing', 'start_date' => '2026-03-01']);

        $this->actingAs($playerUser, 'sanctum')
            ->getJson('/api/my/health?status=ongoing&date_from=2026-02-01')
            ->assertOk()
            ->assertJsonPath('data.injury_summary.total_injuries', 1)
            ->assertJsonPath('data.injuries.data.0.status', 'ongoing');
    }

    public function test_coach_and_manager_can_manage_assigned_health_records(): void
    {
        $coach = User::factory()->create(['role' => User::ROLE_COACH]);
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $team = $this->team();
        $team->staff()->attach([$coach->id, $manager->id]);
        $player = $this->player($team->id, $this->position()->id);

        $injuryId = $this->actingAs($coach, 'sanctum')
            ->postJson("/api/players/{$player->id}/injuries", $this->injuryPayload())
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->json('data.id');

        $this->actingAs($manager, 'sanctum')
            ->putJson("/api/injuries/{$injuryId}", array_merge($this->injuryPayload(), [
                'status' => 'recovered',
                'end_date' => '2026-04-01',
            ]))
            ->assertOk()
            ->assertJsonPath('data.status', 'recovered');

        $measurementId = $this->actingAs($manager, 'sanctum')
            ->postJson("/api/players/{$player->id}/measurements", $this->measurementPayload())
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->json('data.id');

        $this->actingAs($coach, 'sanctum')
            ->deleteJson("/api/physical-measurements/{$measurementId}")
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_cross_team_health_writes_are_rejected(): void
    {
        $coach = User::factory()->create(['role' => User::ROLE_COACH]);
        $ownTeam = $this->team(['name' => 'Own Health']);
        $otherTeam = $this->team(['name' => 'Other Health']);
        $ownTeam->staff()->attach($coach->id);
        $otherPlayer = $this->player($otherTeam->id, $this->position()->id);

        $this->actingAs($coach, 'sanctum')
            ->postJson("/api/players/{$otherPlayer->id}/injuries", $this->injuryPayload())
            ->assertForbidden()
            ->assertJsonPath('success', false);

        $this->actingAs($coach, 'sanctum')
            ->postJson("/api/players/{$otherPlayer->id}/measurements", $this->measurementPayload())
            ->assertForbidden()
            ->assertJsonPath('success', false);
    }

    public function test_player_cannot_write_health_records(): void
    {
        [$playerUser, $player] = $this->playerUserWithProfile();

        $this->actingAs($playerUser, 'sanctum')
            ->postJson("/api/players/{$player->id}/injuries", $this->injuryPayload())
            ->assertForbidden()
            ->assertJsonPath('success', false);
    }

    public function test_player_health_web_page_renders(): void
    {
        [$playerUser, $player] = $this->playerUserWithProfile();
        $this->injury($player->id, ['injury_type' => 'Shoulder']);
        $this->measurement($player->id, ['weight' => 73]);

        $this->actingAs($playerUser)
            ->get('/player/health')
            ->assertOk()
            ->assertSee('Sağlık & Ölçümler')
            ->assertSee('Shoulder');
    }

    private function playerUserWithProfile(array $overrides = []): array
    {
        $user = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $team = $this->team($overrides['team'] ?? []);
        $player = $this->player($team->id, $this->position()->id, array_merge([
            'user_id' => $user->id,
        ], $overrides['player'] ?? []));

        return [$user, $player];
    }

    private function team(array $overrides = []): Teams
    {
        return Teams::create(array_merge([
            'name' => 'Health Team',
            'age_category' => 'U18',
            'season' => '2025-2026',
            'description' => null,
        ], $overrides));
    }

    private function position(): Positions
    {
        return Positions::create([
            'name' => 'Defender',
            'code' => 'DF'.fake()->unique()->numberBetween(100, 999),
            'description' => null,
        ]);
    }

    private function player(int $teamId, int $positionId, array $overrides = []): Players
    {
        return Players::create(array_merge([
            'team_id' => $teamId,
            'position_id' => $positionId,
            'first_name' => 'Efe',
            'last_name' => 'Kurt',
            'birth_date' => '2008-05-05',
            'jersey_number' => 5,
            'height' => 181,
            'weight' => 74,
            'dominant_foot' => 'right',
            'nationality' => 'TR',
            'status' => 'active',
        ], $overrides));
    }

    private function injury(int $playerId, array $overrides = []): Injuries
    {
        return Injuries::create(array_merge($this->injuryPayload(), [
            'player_id' => $playerId,
        ], $overrides));
    }

    private function measurement(int $playerId, array $overrides = []): PhysicalMeasurements
    {
        return PhysicalMeasurements::create(array_merge($this->measurementPayload(), [
            'player_id' => $playerId,
        ], $overrides));
    }

    private function injuryPayload(): array
    {
        return [
            'injury_type' => 'Hamstring',
            'start_date' => '2026-03-01',
            'end_date' => null,
            'status' => 'ongoing',
            'description' => 'Training load related',
        ];
    }

    private function measurementPayload(): array
    {
        return [
            'measurement_date' => '2026-03-01',
            'height' => 181,
            'weight' => 74,
            'body_fat_percentage' => 12,
            'sprint_time' => 5.1,
            'agility_score' => 8,
            'endurance_level' => 8,
            'strength_score' => 8,
            'note' => 'Baseline',
        ];
    }
}
