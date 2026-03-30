<?php

namespace Database\Seeders;

use App\Models\Players;
use App\Models\Teams;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            PositionSeeder::class,
            TeamSeeder::class,
            PlayerSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Sistem',
            'surname' => 'Yöneticisi',
            'email' => 'admin@test.com',
            'role' => 'super_admin',
            'status' => true,
        ]);

        $coach = User::factory()->create([
            'name' => 'Ahmet',
            'surname' => 'Yılmaz',
            'email' => 'coach@test.com',
            'role' => 'coach',
            'status' => true,
        ]);

        $manager = User::factory()->create([
            'name' => 'Mehmet',
            'surname' => 'Demir',
            'email' => 'manager@test.com',
            'role' => 'manager',
            'status' => true,
        ]);

        // Coach ve Manager'ı Galatasaray'a ata
        $galatasaray = Teams::where('name', 'Galatasaray')->first();
        if ($galatasaray) {
            $galatasaray->coaches()->attach([$coach->id, $manager->id]);
        }

        // Tüm oyunculara kullanıcı hesabı oluştur
        $usedEmails = [];
        Players::all()->each(function (Players $player) use (&$usedEmails) {
            $baseEmail = Str::slug($player->first_name, '.').'.'.Str::slug($player->last_name, '.');
            $email = $baseEmail.'@playerpulse.local';

            // Aynı isimli oyuncular için benzersiz e-posta
            if (in_array($email, $usedEmails)) {
                $email = $baseEmail.'.'.$player->id.'@playerpulse.local';
            }
            $usedEmails[] = $email;

            $user = User::create([
                'name' => $player->first_name,
                'surname' => $player->last_name,
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => 'player',
                'status' => true,
            ]);

            $player->update(['user_id' => $user->id]);
        });
    }
}
