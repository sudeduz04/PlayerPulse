<?php

namespace Database\Seeders;

use App\Models\Teams;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $teams = [
            ['name' => 'Galatasaray', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Galatasaray Spor Kulübü'],
            ['name' => 'Fenerbahçe', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Fenerbahçe Spor Kulübü'],
            ['name' => 'Beşiktaş', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Beşiktaş Jimnastik Kulübü'],
            ['name' => 'Trabzonspor', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Trabzonspor Kulübü'],
            ['name' => 'Başakşehir', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'İstanbul Başakşehir FK'],
            ['name' => 'Adana Demirspor', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Adana Demirspor Kulübü'],
            ['name' => 'Antalyaspor', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Antalyaspor Kulübü'],
            ['name' => 'Alanyaspor', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Alanyaspor Kulübü'],
            ['name' => 'Konyaspor', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Konyaspor Kulübü'],
            ['name' => 'Sivasspor', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Sivasspor Kulübü'],
            ['name' => 'Kasımpaşa', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Kasımpaşa Spor Kulübü'],
            ['name' => 'Samsunspor', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Samsunspor Kulübü'],
            ['name' => 'Kayserispor', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Kayserispor Kulübü'],
            ['name' => 'Gaziantep FK', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Gaziantep Futbol Kulübü'],
            ['name' => 'Hatayspor', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Atakaş Hatayspor'],
            ['name' => 'Rizespor', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Çaykur Rizespor'],
            ['name' => 'Pendikspor', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Pendikspor Kulübü'],
            ['name' => 'İstanbulspor', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'İstanbulspor AŞ'],
            ['name' => 'Bodrum FK', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Bodrum Futbol Kulübü'],
            ['name' => 'Eyüpspor', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Eyüpspor Kulübü'],
        ];

        foreach ($teams as $team) {
            Teams::updateOrCreate(
                ['name' => $team['name'], 'season' => $team['season']],
                $team
            );
        }
    }
}
