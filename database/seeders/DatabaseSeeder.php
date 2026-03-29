<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Ahmet',
            'surname' => 'Yılmaz',
            'email' => 'coach@test.com',
            'role' => 'coach',
            'status' => true,
        ]);

        User::factory()->create([
            'name' => 'Mehmet',
            'surname' => 'Demir',
            'email' => 'manager@test.com',
            'role' => 'manager',
            'status' => true,
        ]);

        User::factory()->create([
            'name' => 'Ali',
            'surname' => 'Kaya',
            'email' => 'player@test.com',
            'role' => 'player',
            'status' => true,
        ]);
    }
}
