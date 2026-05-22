<?php

namespace Tests\Feature;

use App\Models\Lineups;
use App\Models\Matches;
use App\Models\Players;
use App\Models\Positions;
use App\Models\Teams;
use App\Models\User;
use App\Services\Ai\AiProvider;
use App\Services\Ai\NullAiProvider;
use App\Services\SmartLineupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class LineupAndAiModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_coach_can_open_lineups_index_and_create_pages(): void
    {
        $coach = User::factory()->create(['role' => User::ROLE_COACH]);
        $team = $this->team();
        $team->staff()->attach($coach->id);
        $this->match($team->id);

        $this->actingAs($coach)
            ->get('/coach/lineups')
            ->assertOk()
            ->assertSee('Kadrolar');

        $this->actingAs($coach)
            ->get('/coach/lineups/create')
            ->assertOk()
            ->assertSee('Yeni Kadro');
    }

    public function test_coach_can_create_manual_lineup_with_11_players(): void
    {
        $coach = User::factory()->create(['role' => User::ROLE_COACH]);
        $team = $this->team();
        $team->staff()->attach($coach->id);
        $match = $this->match($team->id);
        $position = $this->position();

        $players = collect(range(1, 11))
            ->map(fn ($i) => $this->player($team->id, $position->id, ['jersey_number' => $i]));

        $payload = [
            'match_id' => $match->id,
            'formation' => '4-4-2',
            'note' => 'Test lineup',
            'players' => $players->map(fn ($p) => [
                'player_id' => $p->id,
                'position_id' => $position->id,
            ])->all(),
        ];

        $this->actingAs($coach)
            ->post('/coach/lineups', $payload)
            ->assertRedirect();

        $lineup = Lineups::first();
        $this->assertNotNull($lineup);
        $this->assertEquals('4-4-2', $lineup->formation);
        $this->assertFalse($lineup->is_ai_generated);
        $this->assertEquals(11, $lineup->players()->count());
    }

    public function test_coach_can_manage_lineups_through_api(): void
    {
        $coach = User::factory()->create(['role' => User::ROLE_COACH]);
        $team = $this->team();
        $team->staff()->attach($coach->id);
        $match = $this->match($team->id);
        $position = $this->position();

        $players = collect(range(1, 11))
            ->map(fn ($i) => $this->player($team->id, $position->id, ['jersey_number' => $i]));

        $this->actingAs($coach, 'sanctum')
            ->getJson('/api/lineups/options')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data.matches')
            ->assertJsonCount(1, 'data.positions');

        $this->actingAs($coach, 'sanctum')
            ->getJson("/api/matches/{$match->id}/roster")
            ->assertOk()
            ->assertJsonCount(11, 'data');

        $create = $this->actingAs($coach, 'sanctum')
            ->postJson('/api/lineups', [
                'match_id' => $match->id,
                'formation' => '4-2-3-1',
                'note' => 'API lineup',
                'players' => $players->map(fn ($p) => [
                    'player_id' => $p->id,
                    'position_id' => $position->id,
                ])->all(),
            ])
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.formation', '4-2-3-1');

        $lineupId = $create->json('data.id');

        $this->actingAs($coach, 'sanctum')
            ->getJson("/api/lineups/{$lineupId}")
            ->assertOk()
            ->assertJsonPath('data.id', $lineupId)
            ->assertJsonCount(11, 'data.players');

        $this->actingAs($coach, 'sanctum')
            ->deleteJson("/api/lineups/{$lineupId}")
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('lineups', ['id' => $lineupId]);
    }

    public function test_ai_analysis_ajax_request_is_queued(): void
    {
        Queue::fake();
        $this->app->instance(AiProvider::class, new FakeAiProvider([
            'overall_score' => 8,
            'summary' => 'Hazir',
            'strengths' => '- Hiz',
            'weaknesses' => '- Deneyim',
            'recommendations' => '- Calisma',
        ]));

        $coach = User::factory()->create(['role' => User::ROLE_COACH]);
        $team = $this->team();
        $team->staff()->attach($coach->id);
        $player = $this->player($team->id, $this->position()->id);

        $this->actingAs($coach)
            ->postJson('/coach/analysis', [
                'player_id' => $player->id,
                'focus' => 'savunma',
            ])
            ->assertAccepted()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'queued');

        $this->assertDatabaseHas('ai_recommendations', [
            'player_id' => $player->id,
            'status' => 'queued',
        ]);
    }

    public function test_smart_squad_page_shows_warning_when_ai_not_configured(): void
    {
        $this->app->instance(AiProvider::class, new NullAiProvider);

        $coach = User::factory()->create(['role' => User::ROLE_COACH]);
        $team = $this->team();
        $team->staff()->attach($coach->id);
        $this->match($team->id);

        $this->actingAs($coach)
            ->get('/coach/smart-squad')
            ->assertOk()
            ->assertSee('Akıllı Kadro Önerisi')
            ->assertSee('AI sağlayıcısı yapılandırılmamış');
    }

    public function test_analysis_pages_render_for_coach(): void
    {
        $this->app->instance(AiProvider::class, new NullAiProvider);

        $coach = User::factory()->create(['role' => User::ROLE_COACH]);
        $team = $this->team();
        $team->staff()->attach($coach->id);
        $this->player($team->id, $this->position()->id);

        $this->actingAs($coach)
            ->get('/coach/analysis')
            ->assertOk()
            ->assertSee('AI Analizler');

        $this->actingAs($coach)
            ->get('/coach/analysis/create')
            ->assertOk()
            ->assertSee('Yeni AI Analizi')
            ->assertSee('AI sağlayıcısı yapılandırılmamış');
    }

    public function test_smart_squad_api_reports_ai_status_and_missing_provider(): void
    {
        $this->app->instance(AiProvider::class, new NullAiProvider);

        $coach = User::factory()->create(['role' => User::ROLE_COACH]);
        $team = $this->team();
        $team->staff()->attach($coach->id);
        $match = $this->match($team->id);

        $this->actingAs($coach, 'sanctum')
            ->getJson('/api/smart-squad/options')
            ->assertOk()
            ->assertJsonPath('data.ai_ready', false)
            ->assertJsonPath('data.ai_provider', 'none');

        $this->actingAs($coach, 'sanctum')
            ->postJson('/api/smart-squad', [
                'match_id' => $match->id,
                'formation' => '4-3-3',
            ])
            ->assertBadRequest()
            ->assertJsonPath('success', false);
    }

    public function test_smart_lineup_completes_missing_ai_player_to_11_and_stores_field_slots(): void
    {
        Queue::fake();

        $coach = User::factory()->create(['role' => User::ROLE_COACH]);
        $team = $this->team();
        $team->staff()->attach($coach->id);
        $match = $this->match($team->id);
        $position = $this->position();

        $players = collect(range(1, 11))
            ->map(fn ($i) => $this->player($team->id, $position->id, ['jersey_number' => $i]));

        $this->app->instance(AiProvider::class, new FakeAiProvider([
            'players' => $players->take(10)->values()->map(fn ($player, $index) => [
                'slot_key' => $index === 0 ? 'GK' : null,
                'player_id' => $player->id,
                'position_id' => $position->id,
                'recommendation_score' => 8,
            ])->all(),
            'note' => '10 oyuncu geldi, sistem tamamlamali.',
        ]));

        $this->actingAs($coach)
            ->post('/coach/smart-squad', [
                'match_id' => $match->id,
                'formation' => '4-4-2',
            ])
            ->assertRedirect();

        $lineup = Lineups::with('players')->first();
        $this->assertNotNull($lineup);
        $this->assertEquals('queued', $lineup->status);

        app(SmartLineupService::class)->processQueuedLineup($lineup->id);

        $lineup->refresh()->load('players');
        $this->assertEquals('completed', $lineup->status);
        $this->assertEquals(11, $lineup->players->count());
        $this->assertNotNull($lineup->players->first()->slot_key);
        $this->assertNotNull($lineup->players->first()->field_x);
        $this->assertNotNull($lineup->players->first()->field_y);
    }

    public function test_analysis_api_exposes_options_and_keeps_manager_read_only(): void
    {
        $this->app->instance(AiProvider::class, new NullAiProvider);

        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $team = $this->team();
        $team->staff()->attach($manager->id);
        $player = $this->player($team->id, $this->position()->id);

        $this->actingAs($manager, 'sanctum')
            ->getJson('/api/analysis/options')
            ->assertOk()
            ->assertJsonPath('data.ai_ready', false)
            ->assertJsonCount(1, 'data.players');

        $this->actingAs($manager, 'sanctum')
            ->getJson('/api/analysis')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->actingAs($manager, 'sanctum')
            ->postJson('/api/analysis', [
                'player_id' => $player->id,
                'focus' => 'test',
            ])
            ->assertForbidden();
    }

    public function test_coach_cannot_create_lineup_for_unassigned_team_match(): void
    {
        $coach = User::factory()->create(['role' => User::ROLE_COACH]);
        $ownTeam = $this->team(['name' => 'Own']);
        $otherTeam = $this->team(['name' => 'Other']);
        $ownTeam->staff()->attach($coach->id);
        $match = $this->match($otherTeam->id);
        $position = $this->position();

        $players = collect(range(1, 11))
            ->map(fn ($i) => $this->player($otherTeam->id, $position->id, ['jersey_number' => $i]));

        $payload = [
            'match_id' => $match->id,
            'formation' => '4-3-3',
            'players' => $players->map(fn ($p) => [
                'player_id' => $p->id,
                'position_id' => $position->id,
            ])->all(),
        ];

        $this->actingAs($coach)
            ->post('/coach/lineups', $payload)
            ->assertForbidden();
    }

    private function team(array $overrides = []): Teams
    {
        return Teams::create(array_merge([
            'name' => 'Lineup Team',
            'age_category' => 'U19',
            'season' => '2025-2026',
            'description' => null,
        ], $overrides));
    }

    private function position(): Positions
    {
        return Positions::create([
            'name' => 'Forward',
            'code' => 'FW'.fake()->unique()->numberBetween(100, 999),
            'description' => null,
        ]);
    }

    private function player(int $teamId, int $positionId, array $overrides = []): Players
    {
        return Players::create(array_merge([
            'team_id' => $teamId,
            'position_id' => $positionId,
            'first_name' => 'Test',
            'last_name' => 'Player'.fake()->unique()->numberBetween(1, 9999),
            'birth_date' => '2005-01-01',
            'jersey_number' => 10,
            'height' => 175,
            'weight' => 70,
            'dominant_foot' => 'right',
            'nationality' => 'TR',
            'status' => 'active',
        ], $overrides));
    }

    private function match(int $teamId): Matches
    {
        return Matches::create([
            'team_id' => $teamId,
            'opponent_team' => 'Rakip FC',
            'match_date' => '2026-06-01',
            'match_type' => 'league',
            'location' => 'home',
        ]);
    }
}

class FakeAiProvider implements AiProvider
{
    public function __construct(private array $response) {}

    public function isConfigured(): bool
    {
        return true;
    }

    public function name(): string
    {
        return 'fake';
    }

    public function generate(string $prompt, array $options = []): string
    {
        return json_encode($this->response);
    }

    public function generateJson(string $prompt, array $options = []): array
    {
        return $this->response;
    }
}
