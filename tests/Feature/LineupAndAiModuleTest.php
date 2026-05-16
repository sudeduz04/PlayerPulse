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
use Illuminate\Foundation\Testing\RefreshDatabase;
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
