<?php

namespace Tests\Feature;

use App\Models\Leagues;
use App\Models\Matches;
use App\Models\Teams;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FixtureModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_create_league_and_import_manual_fixture(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $home = $this->team('Galatasaray U19');
        $away = $this->team('Fenerbahce U19');

        $create = $this->actingAs($superAdmin)
            ->post('/super-admin/leagues', [
                'name' => 'U19 Super Lig',
                'season' => '2026-2027',
                'team_ids' => [$home->id, $away->id],
            ])
            ->assertRedirect();

        $league = Leagues::first();
        $this->assertNotNull($league);
        $this->assertEquals(2, $league->teams()->count());

        $this->actingAs($superAdmin)
            ->post("/super-admin/leagues/{$league->id}/fixtures", [
                'rows' => [[
                    'week' => 1,
                    'date' => '2026-08-15',
                    'home_team' => 'Galatasaray U19',
                    'away_team' => 'Fenerbahce U19',
                    'location' => 'Florya',
                ]],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('matches', [
            'league_id' => $league->id,
            'week' => 1,
            'home_team_id' => $home->id,
            'away_team_id' => $away->id,
            'fixture_source' => 'manual',
        ]);

        $this->assertEquals(1, Matches::count());
    }

    public function test_non_super_admin_cannot_access_fixture_module(): void
    {
        $coach = User::factory()->create(['role' => User::ROLE_COACH]);

        $this->actingAs($coach)
            ->get('/super-admin/leagues')
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
