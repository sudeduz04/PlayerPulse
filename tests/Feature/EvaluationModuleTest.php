<?php

namespace Tests\Feature;

use App\Models\DevelopmentReports;
use App\Models\Players;
use App\Models\Positions;
use App\Models\Teams;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvaluationModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_coach_can_create_evaluation_for_assigned_team_player(): void
    {
        $coach = User::factory()->create(['role' => User::ROLE_COACH]);
        $team = $this->team();
        $team->staff()->attach($coach->id);
        $player = $this->player($team->id, $this->position()->id);

        $this->actingAs($coach, 'sanctum')
            ->postJson("/api/players/{$player->id}/reports", $this->reportPayload())
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.player_id', $player->id)
            ->assertJsonPath('data.overall_score', 8);

        $this->assertDatabaseHas('development_reports', [
            'player_id' => $player->id,
            'created_by' => $coach->id,
        ]);
    }

    public function test_coach_cannot_create_evaluation_for_unassigned_team_player(): void
    {
        $coach = User::factory()->create(['role' => User::ROLE_COACH]);
        $ownTeam = $this->team(['name' => 'Own']);
        $otherTeam = $this->team(['name' => 'Other']);
        $ownTeam->staff()->attach($coach->id);
        $player = $this->player($otherTeam->id, $this->position()->id);

        $this->actingAs($coach, 'sanctum')
            ->postJson("/api/players/{$player->id}/reports", $this->reportPayload())
            ->assertForbidden()
            ->assertJsonPath('success', false);
    }

    public function test_manager_can_read_assigned_evaluations_but_cannot_create(): void
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $team = $this->team();
        $team->staff()->attach($manager->id);
        $player = $this->player($team->id, $this->position()->id);
        $report = $this->report($player->id);

        $this->actingAs($manager, 'sanctum')
            ->getJson('/api/development-reports')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.summary.total_reports', 1);

        $this->actingAs($manager, 'sanctum')
            ->getJson("/api/development-reports/{$report->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $report->id);

        $this->actingAs($manager, 'sanctum')
            ->postJson("/api/players/{$player->id}/reports", $this->reportPayload())
            ->assertForbidden()
            ->assertJsonPath('success', false);
    }

    public function test_player_can_view_only_own_reports(): void
    {
        [$user, $player] = $this->playerUserWithProfile();
        [, $otherPlayer] = $this->playerUserWithProfile([
            'team' => ['name' => 'Other'],
            'player' => ['first_name' => 'Other', 'jersey_number' => 19],
        ]);
        $ownReport = $this->report($player->id, ['overall_score' => 7]);
        $otherReport = $this->report($otherPlayer->id, ['overall_score' => 10]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/my/reports');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.summary.total_reports', 1)
            ->assertJsonPath('data.summary.average_overall', 7)
            ->assertJsonCount(1, 'data.reports.data')
            ->assertJsonPath('data.reports.data.0.id', $ownReport->id);

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/development-reports/{$otherReport->id}")
            ->assertForbidden()
            ->assertJsonPath('success', false);
    }

    public function test_evaluation_web_pages_render_for_coach_and_player(): void
    {
        $coach = User::factory()->create(['role' => User::ROLE_COACH]);
        $team = $this->team();
        $team->staff()->attach($coach->id);
        $player = $this->player($team->id, $this->position()->id);
        $report = $this->report($player->id, ['overall_score' => 9, 'strengths' => 'Strong pressing']);

        $this->actingAs($coach)
            ->get('/coach/evaluations')
            ->assertOk()
            ->assertSee('Değerlendirmeler')
            ->assertSee($player->first_name);

        $this->actingAs($coach)
            ->get('/coach/evaluations/create')
            ->assertOk()
            ->assertSee('Yeni Değerlendirme');

        [$playerUser, $linkedPlayer] = $this->playerUserWithProfile([
            'team' => ['name' => 'Player Team'],
            'player' => ['first_name' => 'Linked', 'jersey_number' => 22],
        ]);
        $this->report($linkedPlayer->id, ['strengths' => 'Fast recovery']);

        $this->actingAs($playerUser)
            ->get('/player/reports')
            ->assertOk()
            ->assertSee('Gelişim Raporlarım')
            ->assertSee('Fast recovery');

        $this->actingAs($coach)
            ->get("/coach/evaluations/{$report->id}")
            ->assertOk()
            ->assertSee('Strong pressing');
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
            'name' => 'Evaluation Team',
            'age_category' => 'U19',
            'season' => '2025-2026',
            'description' => null,
        ], $overrides));
    }

    private function position(): Positions
    {
        return Positions::create([
            'name' => 'Winger',
            'code' => 'WG'.fake()->unique()->numberBetween(100, 999),
            'description' => null,
        ]);
    }

    private function player(int $teamId, int $positionId, array $overrides = []): Players
    {
        return Players::create(array_merge([
            'team_id' => $teamId,
            'position_id' => $positionId,
            'first_name' => 'Arda',
            'last_name' => 'Koc',
            'birth_date' => '2008-06-14',
            'jersey_number' => 11,
            'height' => 176,
            'weight' => 69,
            'dominant_foot' => 'right',
            'nationality' => 'TR',
            'status' => 'active',
        ], $overrides));
    }

    private function report(int $playerId, array $overrides = []): DevelopmentReports
    {
        $creator = User::factory()->create(['role' => User::ROLE_COACH]);

        return DevelopmentReports::create(array_merge($this->reportPayload(), [
            'player_id' => $playerId,
            'created_by' => $creator->id,
        ], $overrides));
    }

    private function reportPayload(): array
    {
        return [
            'report_date' => '2026-04-01',
            'technical_development' => 8,
            'physical_development' => 7,
            'tactical_development' => 8,
            'mental_development' => 7,
            'overall_score' => 8,
            'strengths' => 'Strong pressing',
            'weaknesses' => 'Needs first touch work',
            'recommendations' => 'Add extra rondo sessions',
        ];
    }
}
