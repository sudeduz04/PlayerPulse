<?php

namespace Database\Seeders;

use App\Models\Teams;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        // 2025-2026 Trendyol Süper Lig — 18 takım
        $teams = [
            ['name' => 'Galatasaray', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Galatasaray Spor Kulübü — Rams Park'],
            ['name' => 'Fenerbahçe', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Fenerbahçe Spor Kulübü — Şükrü Saracoğlu'],
            ['name' => 'Beşiktaş', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Beşiktaş Jimnastik Kulübü — Tüpraş Stadyumu'],
            ['name' => 'Trabzonspor', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Trabzonspor Kulübü — Papara Park'],
            ['name' => 'Samsunspor', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Yılport Samsunspor — 19 Mayıs Stadyumu'],
            ['name' => 'Göztepe', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Göztepe SK — Gürsel Aksel Stadyumu'],
            ['name' => 'Başakşehir', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'İstanbul Başakşehir FK — Fatih Terim Stadyumu'],
            ['name' => 'Antalyaspor', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Bitexen Antalyaspor — Corendon Airlines Park'],
            ['name' => 'Konyaspor', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Tümosan Konyaspor — Konya Büyükşehir Stadyumu'],
            ['name' => 'Eyüpspor', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Eyüpspor — Atatürk Olimpiyat Stadyumu'],
            ['name' => 'Kasımpaşa', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Kasımpaşa SK — Recep Tayyip Erdoğan Stadyumu'],
            ['name' => 'Kayserispor', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Mondihome Kayserispor — RHG Enertürk Enerji Stadyumu'],
            ['name' => 'Gaziantep FK', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Gaziantep Futbol Kulübü — Kalyon Stadyumu'],
            ['name' => 'Alanyaspor', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Corendon Alanyaspor — Bahçeşehir Okulları Stadyumu'],
            ['name' => 'Çaykur Rizespor', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Çaykur Rizespor — Çaykur Didi Stadyumu'],
            ['name' => 'Kocaelispor', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Kocaelispor — Kocaeli Stadyumu (yeni çıkan)'],
            ['name' => 'Fatih Karagümrük', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Fatih Karagümrük SK — Atatürk Olimpiyat Stadyumu (yeni çıkan)'],
            ['name' => 'Gençlerbirliği', 'age_category' => 'Senior', 'season' => '2025-2026', 'description' => 'Gençlerbirliği SK — Eryaman Stadyumu (yeni çıkan)'],
        ];

        foreach ($teams as $team) {
            Teams::updateOrCreate(
                ['name' => $team['name'], 'season' => $team['season']],
                $team
            );
        }
    }
}
