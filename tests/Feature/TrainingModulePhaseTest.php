<?php

namespace Tests\Feature;

use App\Models\Players;
use App\Models\PlayerTrainingPerformances;
use App\Models\Positions;
use App\Models\Teams;
use App\Models\Trainings;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainingModulePhaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_player_can_view_only_own_training_history_with_summary(): void
    {
        [$playerUser, $player] = $this->playerUserWithProfile();
        [, $otherPlayer] = $this->playerUserWithProfile([
            'team' => ['name' => 'Other Team'],
            'player' => ['first_name' => 'Other', 'jersey_number' => 7],
        ]);

        $trainingOne = $this->training($player->team_id, ['training_date' => '2026-01-10', 'title' => 'Session A']);
        $trainingTwo = $this->training($player->team_id, ['training_date' => '2026-01-15', 'title' => 'Session B']);
        $otherTraining = $this->training($otherPlayer->team_id, ['training_date' => '2026-01-20', 'title' => 'Hidden']);

        $this->performance($trainingOne->id, $player->id, [
            'attendance_status' => 'attended',
            'performance_score' => 8,
        ]);
        $this->performance($trainingTwo->id, $player->id, [
            'attendance_status' => 'absent',
            'performance_score' => null,
        ]);
        $this->performance($otherTraining->id, $otherPlayer->id, [
            'attendance_status' => 'attended',
            'performance_score' => 10,
        ]);

        $response = $this->actingAs($playerUser, 'sanctum')
            ->getJson('/api/my/trainings');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.summary.total_trainings', 2)
            ->assertJsonPath('data.summary.attended', 1)
            ->assertJsonPath('data.summary.absent', 1)
            ->assertJsonPath('data.summary.excused', 0)
            ->assertJsonPath('data.summary.attendance_rate', 50)
            ->assertJsonPath('data.summary.average_score', 8)
            ->assertJsonCount(2, 'data.performances.data');

        $titles = collect($response->json('data.performances.data'))
            ->pluck('training.title');

        $this->assertTrue($titles->contains('Session A'));
        $this->assertTrue($titles->contains('Session B'));
        $this->assertFalse($titles->contains('Hidden'));
    }

    public function test_player_training_history_filters_by_attendance_and_date(): void
    {
        [$playerUser, $player] = $this->playerUserWithProfile();

        $this->performance(
            $this->training($player->team_id, ['training_date' => '2026-02-01'])->id,
            $player->id,
            ['attendance_status' => 'attended', 'performance_score' => 7]
        );
        $this->performance(
            $this->training($player->team_id, ['training_date' => '2026-02-10'])->id,
            $player->id,
            ['attendance_status' => 'excused', 'performance_score' => null]
        );

        $this->actingAs($playerUser, 'sanctum')
            ->getJson('/api/my/trainings?attendance_status=excused&date_from=2026-02-05')
            ->assertOk()
            ->assertJsonPath('data.summary.total_trainings', 1)
            ->assertJsonPath('data.summary.excused', 1)
            ->assertJsonPath('data.performances.data.0.attendance_status', 'excused');
    }

    public function test_non_player_roles_cannot_use_player_training_history_endpoint(): void
    {
        $coach = User::factory()->create(['role' => User::ROLE_COACH]);

        $this->actingAs($coach, 'sanctum')
            ->getJson('/api/my/trainings')
            ->assertForbidden()
            ->assertJsonPath('success', false);
    }

    public function test_coach_training_writes_are_limited_to_assigned_teams(): void
    {
        $coach = User::factory()->create(['role' => User::ROLE_COACH]);
        $ownTeam = $this->team(['name' => 'Own Team']);
        $otherTeam = $this->team(['name' => 'Other Team']);
        $ownTeam->staff()->attach($coach->id);

        $trainingId = $this->actingAs($coach, 'sanctum')
            ->postJson('/api/trainings', $this->trainingPayload($ownTeam->id))
            ->assertCreated()
            ->json('data.id');

        $this->actingAs($coach, 'sanctum')
            ->patchJson("/api/trainings/{$trainingId}", array_merge(
                $this->trainingPayload($ownTeam->id),
                ['title' => 'Updated Session']
            ))
            ->assertOk()
            ->assertJsonPath('data.title', 'Updated Session');

        $this->actingAs($coach, 'sanctum')
            ->deleteJson("/api/trainings/{$trainingId}")
            ->assertOk();

        $this->actingAs($coach, 'sanctum')
            ->postJson('/api/trainings', $this->trainingPayload($otherTeam->id))
            ->assertForbidden()
            ->assertJsonPath('success', false);
    }

    public function test_manager_can_read_assigned_trainings_but_cannot_write(): void
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $team = $this->team();
        $team->staff()->attach($manager->id);
        $training = $this->training($team->id);

        $this->actingAs($manager, 'sanctum')
            ->getJson('/api/trainings')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->actingAs($manager, 'sanctum')
            ->getJson("/api/trainings/{$training->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $training->id);

        $this->actingAs($manager, 'sanctum')
            ->postJson('/api/trainings', $this->trainingPayload($team->id))
            ->assertForbidden()
            ->assertJsonPath('success', false);

        $this->actingAs($manager, 'sanctum')
            ->patchJson("/api/trainings/{$training->id}", $this->trainingPayload($team->id))
            ->assertForbidden()
            ->assertJsonPath('success', false);
    }

    public function test_super_admin_can_create_training_for_any_team(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $team = $this->team();

        $this->actingAs($superAdmin, 'sanctum')
            ->postJson('/api/trainings', $this->trainingPayload($team->id))
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.team_id', $team->id);
    }

    public function test_player_training_history_web_page_renders_for_linked_player(): void
    {
        [$playerUser, $player] = $this->playerUserWithProfile();
        $training = $this->training($player->team_id, ['title' => 'Web Session']);
        $this->performance($training->id, $player->id, ['attendance_status' => 'attended']);

        $this->actingAs($playerUser)
            ->get('/player/trainings')
            ->assertOk()
            ->assertSee('Antrenman Geçmişi')
            ->assertSee('Web Session');
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
            'name' => 'Phase Team',
            'age_category' => 'U18',
            'season' => '2025-2026',
            'description' => null,
        ], $overrides));
    }

    private function position(): Positions
    {
        return Positions::create([
            'name' => 'Midfielder',
            'code' => 'MF'.fake()->unique()->numberBetween(100, 999),
            'description' => null,
        ]);
    }

    private function player(int $teamId, int $positionId, array $overrides = []): Players
    {
        return Players::create(array_merge([
            'team_id' => $teamId,
            'position_id' => $positionId,
            'first_name' => 'Mert',
            'last_name' => 'Aydin',
            'birth_date' => '2008-04-12',
            'jersey_number' => 8,
            'height' => 175,
            'weight' => 68,
            'dominant_foot' => 'right',
            'nationality' => 'TR',
            'status' => 'active',
        ], $overrides));
    }

    private function training(int $teamId, array $overrides = []): Trainings
    {
        $creator = User::factory()->create(['role' => User::ROLE_COACH]);

        return Trainings::create(array_merge($this->trainingPayload($teamId), [
            'created_by' => $creator->id,
        ], $overrides));
    }

    private function performance(int $trainingId, int $playerId, array $overrides = []): PlayerTrainingPerformances
    {
        return PlayerTrainingPerformances::create(array_merge([
            'training_id' => $trainingId,
            'player_id' => $playerId,
            'attendance_status' => 'attended',
            'performance_score' => 8,
            'speed_score' => 8,
            'endurance_score' => 8,
            'technique_score' => 8,
            'discipline_score' => 8,
            'coach_comment' => 'Good work',
        ], $overrides));
    }

    private function trainingPayload(int $teamId): array
    {
        return [
            'team_id' => $teamId,
            'title' => 'Phase Training',
            'training_date' => '2026-03-01',
            'training_type' => 'technical',
            'duration_minutes' => 90,
            'description' => 'Phase test',
            'coach_note' => 'Keep tempo',
        ];
    }
}
