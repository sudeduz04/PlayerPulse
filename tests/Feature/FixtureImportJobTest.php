<?php

namespace Tests\Feature;

use App\Jobs\ImportFixtureFileJob;
use App\Models\FixtureImports;
use App\Models\Leagues;
use App\Models\Teams;
use App\Models\User;
use App\Services\FixtureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FixtureImportJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_file_upload_creates_fixture_import_and_dispatches_job(): void
    {
        Bus::fake();
        Storage::fake();

        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $home = $this->team('Alfa');
        $away = $this->team('Beta');
        $league = Leagues::create(['name' => 'Test Lig', 'season' => '2026-2027']);
        $league->teams()->sync([$home->id, $away->id]);

        $csv = "week,date,home_team,away_team,location,status\n1,2026-08-15,Alfa,Beta,Saha,scheduled\n";
        $file = UploadedFile::fake()->createWithContent('fixture.csv', $csv);

        $this->actingAs($superAdmin)
            ->post("/super-admin/leagues/{$league->id}/fixtures", [
                'fixture_file' => $file,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('fixture_imports', [
            'league_id' => $league->id,
            'source' => 'file',
            'status' => 'queued',
        ]);

        Bus::assertDispatched(ImportFixtureFileJob::class);
    }

    public function test_process_stored_file_creates_matches_with_status(): void
    {
        Storage::fake();

        $home = $this->team('Alfa');
        $away = $this->team('Beta');
        $league = Leagues::create(['name' => 'Test Lig', 'season' => '2026-2027']);
        $league->teams()->sync([$home->id, $away->id]);

        $csv = "week,date,home_team,away_team,location,status\n1,2026-08-15,Alfa,Beta,Saha,scheduled\n2,2026-08-22,Beta,Alfa,Saha,finished\n";
        Storage::put('fixtures/imports/test.csv', $csv);

        $import = FixtureImports::create([
            'league_id' => $league->id,
            'file_path' => 'fixtures/imports/test.csv',
            'original_filename' => 'fixture.csv',
            'source' => 'file',
            'status' => 'queued',
        ]);

        app(FixtureService::class)->processStoredFile($import->id);

        $import->refresh();
        $this->assertEquals('completed', $import->status);
        $this->assertEquals(2, $import->created_rows);
        $this->assertDatabaseHas('matches', [
            'league_id' => $league->id,
            'week' => 1,
            'status' => 'scheduled',
        ]);
        $this->assertDatabaseHas('matches', [
            'league_id' => $league->id,
            'week' => 2,
            'status' => 'finished',
        ]);
    }

    public function test_status_endpoint_returns_import_progress(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $league = Leagues::create(['name' => 'Test Lig', 'season' => '2026-2027']);
        $import = FixtureImports::create([
            'league_id' => $league->id,
            'source' => 'manual',
            'status' => 'running',
            'created_rows' => 5,
        ]);

        $this->actingAs($superAdmin)
            ->get("/super-admin/leagues/{$league->id}/imports/{$import->id}/status")
            ->assertOk()
            ->assertJsonPath('data.status', 'running')
            ->assertJsonPath('data.status_label', 'İşleniyor')
            ->assertJsonPath('data.created_rows', 5);
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
