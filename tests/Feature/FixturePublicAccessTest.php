<?php

namespace Tests\Feature;

use App\Models\Leagues;
use App\Models\Matches;
use App\Models\Teams;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FixturePublicAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_roles_can_view_fixtures_index_and_show(): void
    {
        $home = Teams::create(['name' => 'A FC', 'age_category' => 'Senior', 'season' => '2025-2026']);
        $away = Teams::create(['name' => 'B FC', 'age_category' => 'Senior', 'season' => '2025-2026']);
        $league = Leagues::create(['name' => 'Test Lig', 'season' => '2025-2026']);
        $league->teams()->sync([$home->id, $away->id]);

        Matches::create([
            'league_id' => $league->id, 'week' => 1,
            'team_id' => $home->id, 'home_team_id' => $home->id, 'away_team_id' => $away->id,
            'opponent_team' => 'B FC', 'match_date' => '2025-08-08', 'match_type' => 'league',
            'fixture_source' => 'seeder', 'status' => 'finished',
            'goals_for' => 2, 'goals_against' => 1, 'result' => 'home_win',
        ]);
        Matches::create([
            'league_id' => $league->id, 'week' => 2,
            'team_id' => $away->id, 'home_team_id' => $away->id, 'away_team_id' => $home->id,
            'opponent_team' => 'A FC', 'match_date' => '2025-08-15', 'match_type' => 'league',
            'fixture_source' => 'seeder', 'status' => 'scheduled',
            'goals_for' => 0, 'goals_against' => 0,
        ]);

        foreach (['super_admin', 'manager', 'coach', 'player'] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)->get('/fixtures')
                ->assertOk()
                ->assertSee('Test Lig');

            $this->actingAs($user)->get("/fixtures/{$league->id}")
                ->assertOk()
                ->assertSee('Test Lig')
                ->assertSee('2. Hafta');  // ilk oynanmamış hafta default açılır
        }
    }

    public function test_guest_cannot_access_fixtures(): void
    {
        $this->get('/fixtures')->assertRedirect('/login');
    }

    public function test_finished_match_shows_winner_name_in_turkish(): void
    {
        $home = Teams::create(['name' => 'Galatasaray Test', 'age_category' => 'Senior', 'season' => '2025-2026']);
        $away = Teams::create(['name' => 'Fenerbahçe Test', 'age_category' => 'Senior', 'season' => '2025-2026']);
        $league = Leagues::create(['name' => 'Test Lig', 'season' => '2025-2026']);
        $league->teams()->sync([$home->id, $away->id]);

        Matches::create([
            'league_id' => $league->id, 'week' => 1,
            'team_id' => $home->id, 'home_team_id' => $home->id, 'away_team_id' => $away->id,
            'opponent_team' => $away->name, 'match_date' => '2025-08-08', 'match_type' => 'league',
            'fixture_source' => 'seeder', 'status' => 'finished',
            'goals_for' => 3, 'goals_against' => 1, 'result' => 'home_win',
        ]);

        $coach = User::factory()->create(['role' => 'coach']);

        $this->actingAs($coach)->get("/fixtures/{$league->id}?week=1")
            ->assertOk()
            ->assertSee('Galatasaray Test kazandı')
            ->assertSee('Tamamlandı');
    }
}
