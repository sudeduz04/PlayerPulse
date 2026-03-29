<?php

namespace Database\Seeders;

use App\Models\Positions;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            ['name' => 'Kaleci', 'code' => 'GK', 'description' => 'Kaleyi koruyan oyuncu'],
            ['name' => 'Stoper', 'code' => 'CB', 'description' => 'Merkez defans oyuncusu'],
            ['name' => 'Sol Bek', 'code' => 'LB', 'description' => 'Sol kanat defans oyuncusu'],
            ['name' => 'Sağ Bek', 'code' => 'RB', 'description' => 'Sağ kanat defans oyuncusu'],
            ['name' => 'Defansif Orta Saha', 'code' => 'CDM', 'description' => 'Savunmacı orta saha oyuncusu'],
            ['name' => 'Merkez Orta Saha', 'code' => 'CM', 'description' => 'Merkez orta saha oyuncusu'],
            ['name' => 'Ofansif Orta Saha', 'code' => 'CAM', 'description' => 'Hücumcu orta saha oyuncusu'],
            ['name' => 'Sol Kanat', 'code' => 'LW', 'description' => 'Sol kanat hücum oyuncusu'],
            ['name' => 'Sağ Kanat', 'code' => 'RW', 'description' => 'Sağ kanat hücum oyuncusu'],
            ['name' => 'Santrafor', 'code' => 'ST', 'description' => 'Merkez forvet oyuncusu'],
            ['name' => 'İkinci Forvet', 'code' => 'CF', 'description' => 'Santraforun arkasında oynayan forvet'],
        ];

        foreach ($positions as $position) {
            Positions::updateOrCreate(
                ['code' => $position['code']],
                $position
            );
        }
    }
}
