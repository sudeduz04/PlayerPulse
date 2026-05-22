<?php

namespace Database\Seeders;

use App\Models\Players;
use App\Models\Positions;
use App\Models\Teams;
use Illuminate\Database\Seeder;

class PlayerSeeder extends Seeder
{
    public function run(): void
    {
        $positions = Positions::pluck('id', 'code');
        $teams = Teams::where('season', '2025-2026')->pluck('id', 'name');

        $players = [
            // ===== Galatasaray (2025-2026) =====
            ['team' => 'Galatasaray', 'first_name' => 'Uğurcan', 'last_name' => 'Çakır', 'jersey_number' => 1, 'position' => 'GK', 'birth_date' => '1996-04-05', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 191, 'weight' => 80],
            ['team' => 'Galatasaray', 'first_name' => 'Wilfried', 'last_name' => 'Singo', 'jersey_number' => 4, 'position' => 'CB', 'birth_date' => '2000-12-25', 'dominant_foot' => 'right', 'nationality' => 'Fildişi Sahili', 'height' => 191, 'weight' => 87],
            ['team' => 'Galatasaray', 'first_name' => 'Davinson', 'last_name' => 'Sánchez', 'jersey_number' => 6, 'position' => 'CB', 'birth_date' => '1996-06-12', 'dominant_foot' => 'right', 'nationality' => 'Kolombiya', 'height' => 187, 'weight' => 82],
            ['team' => 'Galatasaray', 'first_name' => 'Eren', 'last_name' => 'Elmalı', 'jersey_number' => 24, 'position' => 'LB', 'birth_date' => '2000-09-23', 'dominant_foot' => 'left', 'nationality' => 'Türkiye', 'height' => 180, 'weight' => 75],
            ['team' => 'Galatasaray', 'first_name' => 'Kaan', 'last_name' => 'Ayhan', 'jersey_number' => 5, 'position' => 'CB', 'birth_date' => '1994-11-10', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 184, 'weight' => 78],
            ['team' => 'Galatasaray', 'first_name' => 'Lucas', 'last_name' => 'Torreira', 'jersey_number' => 34, 'position' => 'CDM', 'birth_date' => '1996-02-11', 'dominant_foot' => 'right', 'nationality' => 'Uruguay', 'height' => 168, 'weight' => 65],
            ['team' => 'Galatasaray', 'first_name' => 'İlkay', 'last_name' => 'Gündoğan', 'jersey_number' => 19, 'position' => 'CM', 'birth_date' => '1990-10-24', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 180, 'weight' => 75],
            ['team' => 'Galatasaray', 'first_name' => 'Gabriel', 'last_name' => 'Sara', 'jersey_number' => 8, 'position' => 'CM', 'birth_date' => '1999-06-27', 'dominant_foot' => 'right', 'nationality' => 'Brezilya', 'height' => 174, 'weight' => 70],
            ['team' => 'Galatasaray', 'first_name' => 'Leroy', 'last_name' => 'Sané', 'jersey_number' => 10, 'position' => 'RW', 'birth_date' => '1996-01-11', 'dominant_foot' => 'left', 'nationality' => 'Almanya', 'height' => 183, 'weight' => 78],
            ['team' => 'Galatasaray', 'first_name' => 'Barış Alper', 'last_name' => 'Yılmaz', 'jersey_number' => 17, 'position' => 'LW', 'birth_date' => '2000-05-23', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 172, 'weight' => 66],
            ['team' => 'Galatasaray', 'first_name' => 'Victor', 'last_name' => 'Osimhen', 'jersey_number' => 45, 'position' => 'ST', 'birth_date' => '1998-12-29', 'dominant_foot' => 'right', 'nationality' => 'Nijerya', 'height' => 185, 'weight' => 78],
            ['team' => 'Galatasaray', 'first_name' => 'Mauro', 'last_name' => 'Icardi', 'jersey_number' => 9, 'position' => 'ST', 'birth_date' => '1993-02-19', 'dominant_foot' => 'right', 'nationality' => 'Arjantin', 'height' => 181, 'weight' => 75],
            ['team' => 'Galatasaray', 'first_name' => 'Yunus', 'last_name' => 'Akgün', 'jersey_number' => 11, 'position' => 'RW', 'birth_date' => '2000-07-07', 'dominant_foot' => 'left', 'nationality' => 'Türkiye', 'height' => 178, 'weight' => 72],

            // ===== Fenerbahçe (2025-2026) =====
            ['team' => 'Fenerbahçe', 'first_name' => 'Ederson', 'last_name' => 'Moraes', 'jersey_number' => 1, 'position' => 'GK', 'birth_date' => '1993-08-17', 'dominant_foot' => 'right', 'nationality' => 'Brezilya', 'height' => 188, 'weight' => 86],
            ['team' => 'Fenerbahçe', 'first_name' => 'İrfan Can', 'last_name' => 'Eğribayat', 'jersey_number' => 50, 'position' => 'GK', 'birth_date' => '1998-06-30', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 191, 'weight' => 85],
            ['team' => 'Fenerbahçe', 'first_name' => 'Milan', 'last_name' => 'Škriniar', 'jersey_number' => 37, 'position' => 'CB', 'birth_date' => '1995-02-11', 'dominant_foot' => 'right', 'nationality' => 'Slovakya', 'height' => 188, 'weight' => 80],
            ['team' => 'Fenerbahçe', 'first_name' => 'Çağlar', 'last_name' => 'Söyüncü', 'jersey_number' => 4, 'position' => 'CB', 'birth_date' => '1996-05-23', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 187, 'weight' => 82],
            ['team' => 'Fenerbahçe', 'first_name' => 'Jayden', 'last_name' => 'Oosterwolde', 'jersey_number' => 19, 'position' => 'LB', 'birth_date' => '2001-04-25', 'dominant_foot' => 'left', 'nationality' => 'Hollanda', 'height' => 185, 'weight' => 76],
            ['team' => 'Fenerbahçe', 'first_name' => 'Bright', 'last_name' => 'Osayi-Samuel', 'jersey_number' => 88, 'position' => 'RB', 'birth_date' => '1997-12-31', 'dominant_foot' => 'right', 'nationality' => 'Nijerya', 'height' => 179, 'weight' => 73],
            ['team' => 'Fenerbahçe', 'first_name' => 'İsmail', 'last_name' => 'Yüksek', 'jersey_number' => 5, 'position' => 'CDM', 'birth_date' => '1999-04-01', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 180, 'weight' => 73],
            ['team' => 'Fenerbahçe', 'first_name' => 'Sebastian', 'last_name' => 'Szymański', 'jersey_number' => 87, 'position' => 'CAM', 'birth_date' => '1999-05-10', 'dominant_foot' => 'left', 'nationality' => 'Polonya', 'height' => 175, 'weight' => 68],
            ['team' => 'Fenerbahçe', 'first_name' => 'Anderson', 'last_name' => 'Talisca', 'jersey_number' => 10, 'position' => 'CAM', 'birth_date' => '1994-02-01', 'dominant_foot' => 'right', 'nationality' => 'Brezilya', 'height' => 191, 'weight' => 80],
            ['team' => 'Fenerbahçe', 'first_name' => 'Youssef', 'last_name' => 'En-Nesyri', 'jersey_number' => 7, 'position' => 'ST', 'birth_date' => '1997-06-01', 'dominant_foot' => 'right', 'nationality' => 'Fas', 'height' => 189, 'weight' => 70],
            ['team' => 'Fenerbahçe', 'first_name' => 'Dušan', 'last_name' => 'Tadić', 'jersey_number' => 9, 'position' => 'LW', 'birth_date' => '1988-11-20', 'dominant_foot' => 'left', 'nationality' => 'Sırbistan', 'height' => 181, 'weight' => 80],
            ['team' => 'Fenerbahçe', 'first_name' => 'İrfan Can', 'last_name' => 'Kahveci', 'jersey_number' => 7, 'position' => 'CAM', 'birth_date' => '1995-07-15', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 175, 'weight' => 72],
            ['team' => 'Fenerbahçe', 'first_name' => 'Oğuz', 'last_name' => 'Aydın', 'jersey_number' => 14, 'position' => 'RW', 'birth_date' => '1999-08-08', 'dominant_foot' => 'left', 'nationality' => 'Türkiye', 'height' => 174, 'weight' => 70],

            // ===== Beşiktaş (2025-2026) =====
            ['team' => 'Beşiktaş', 'first_name' => 'Mert', 'last_name' => 'Günok', 'jersey_number' => 1, 'position' => 'GK', 'birth_date' => '1989-03-01', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 194, 'weight' => 86],
            ['team' => 'Beşiktaş', 'first_name' => 'Ersin', 'last_name' => 'Destanoğlu', 'jersey_number' => 35, 'position' => 'GK', 'birth_date' => '2001-01-01', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 190, 'weight' => 82],
            ['team' => 'Beşiktaş', 'first_name' => 'Gabriel', 'last_name' => 'Paulista', 'jersey_number' => 4, 'position' => 'CB', 'birth_date' => '1990-11-26', 'dominant_foot' => 'right', 'nationality' => 'Brezilya', 'height' => 187, 'weight' => 79],
            ['team' => 'Beşiktaş', 'first_name' => 'Felix', 'last_name' => 'Uduokhai', 'jersey_number' => 5, 'position' => 'CB', 'birth_date' => '1997-09-09', 'dominant_foot' => 'left', 'nationality' => 'Almanya', 'height' => 192, 'weight' => 84],
            ['team' => 'Beşiktaş', 'first_name' => 'Jonas', 'last_name' => 'Svensson', 'jersey_number' => 2, 'position' => 'RB', 'birth_date' => '1993-03-06', 'dominant_foot' => 'right', 'nationality' => 'Norveç', 'height' => 180, 'weight' => 74],
            ['team' => 'Beşiktaş', 'first_name' => 'Arthur', 'last_name' => 'Masuaku', 'jersey_number' => 13, 'position' => 'LB', 'birth_date' => '1993-11-07', 'dominant_foot' => 'left', 'nationality' => 'Kongo', 'height' => 179, 'weight' => 73],
            ['team' => 'Beşiktaş', 'first_name' => 'Wilfred', 'last_name' => 'Ndidi', 'jersey_number' => 25, 'position' => 'CDM', 'birth_date' => '1996-12-16', 'dominant_foot' => 'right', 'nationality' => 'Nijerya', 'height' => 183, 'weight' => 75],
            ['team' => 'Beşiktaş', 'first_name' => 'Salih', 'last_name' => 'Uçan', 'jersey_number' => 8, 'position' => 'CM', 'birth_date' => '1994-01-06', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 176, 'weight' => 68],
            ['team' => 'Beşiktaş', 'first_name' => 'Rafa', 'last_name' => 'Silva', 'jersey_number' => 10, 'position' => 'CAM', 'birth_date' => '1993-05-17', 'dominant_foot' => 'left', 'nationality' => 'Portekiz', 'height' => 174, 'weight' => 66],
            ['team' => 'Beşiktaş', 'first_name' => 'Ernest', 'last_name' => 'Muçi', 'jersey_number' => 7, 'position' => 'RW', 'birth_date' => '2001-03-19', 'dominant_foot' => 'right', 'nationality' => 'Arnavutluk', 'height' => 182, 'weight' => 76],
            ['team' => 'Beşiktaş', 'first_name' => 'Vaclav', 'last_name' => 'Černý', 'jersey_number' => 17, 'position' => 'LW', 'birth_date' => '1997-10-17', 'dominant_foot' => 'left', 'nationality' => 'Çek Cum.', 'height' => 175, 'weight' => 72],
            ['team' => 'Beşiktaş', 'first_name' => 'Tammy', 'last_name' => 'Abraham', 'jersey_number' => 18, 'position' => 'ST', 'birth_date' => '1997-10-02', 'dominant_foot' => 'right', 'nationality' => 'İngiltere', 'height' => 194, 'weight' => 85],
            ['team' => 'Beşiktaş', 'first_name' => 'Cenk', 'last_name' => 'Tosun', 'jersey_number' => 23, 'position' => 'ST', 'birth_date' => '1991-06-07', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 183, 'weight' => 78],

            // ===== Trabzonspor (2025-2026) =====
            ['team' => 'Trabzonspor', 'first_name' => 'Andre', 'last_name' => 'Onana', 'jersey_number' => 1, 'position' => 'GK', 'birth_date' => '1996-04-02', 'dominant_foot' => 'right', 'nationality' => 'Kamerun', 'height' => 190, 'weight' => 88],
            ['team' => 'Trabzonspor', 'first_name' => 'Onuralp', 'last_name' => 'Çevikkan', 'jersey_number' => 12, 'position' => 'GK', 'birth_date' => '2000-09-11', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 187, 'weight' => 80],
            ['team' => 'Trabzonspor', 'first_name' => 'Stefan', 'last_name' => 'Savić', 'jersey_number' => 14, 'position' => 'CB', 'birth_date' => '1991-01-08', 'dominant_foot' => 'right', 'nationality' => 'Karadağ', 'height' => 187, 'weight' => 78],
            ['team' => 'Trabzonspor', 'first_name' => 'Pina', 'last_name' => 'Manuel', 'jersey_number' => 21, 'position' => 'CM', 'birth_date' => '2000-02-21', 'dominant_foot' => 'left', 'nationality' => 'Portekiz', 'height' => 172, 'weight' => 65],
            ['team' => 'Trabzonspor', 'first_name' => 'Anastasios', 'last_name' => 'Bakasetas', 'jersey_number' => 10, 'position' => 'CAM', 'birth_date' => '1993-06-28', 'dominant_foot' => 'left', 'nationality' => 'Yunanistan', 'height' => 184, 'weight' => 75],
            ['team' => 'Trabzonspor', 'first_name' => 'Edin', 'last_name' => 'Višća', 'jersey_number' => 7, 'position' => 'RW', 'birth_date' => '1990-02-17', 'dominant_foot' => 'left', 'nationality' => 'Bosna Hersek', 'height' => 184, 'weight' => 77],
            ['team' => 'Trabzonspor', 'first_name' => 'Felipe', 'last_name' => 'Augusto', 'jersey_number' => 9, 'position' => 'ST', 'birth_date' => '2003-04-21', 'dominant_foot' => 'right', 'nationality' => 'Brezilya', 'height' => 183, 'weight' => 78],
            ['team' => 'Trabzonspor', 'first_name' => 'Paul', 'last_name' => 'Onuachu', 'jersey_number' => 17, 'position' => 'ST', 'birth_date' => '1994-05-28', 'dominant_foot' => 'right', 'nationality' => 'Nijerya', 'height' => 201, 'weight' => 90],
            ['team' => 'Trabzonspor', 'first_name' => 'Eren', 'last_name' => 'Elmalı', 'jersey_number' => 23, 'position' => 'LB', 'birth_date' => '2000-09-23', 'dominant_foot' => 'left', 'nationality' => 'Türkiye', 'height' => 180, 'weight' => 74],
            ['team' => 'Trabzonspor', 'first_name' => 'Hüseyin', 'last_name' => 'Türkmen', 'jersey_number' => 4, 'position' => 'CB', 'birth_date' => '1999-02-04', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 187, 'weight' => 80],
            ['team' => 'Trabzonspor', 'first_name' => 'Mustafa', 'last_name' => 'Eskihellaç', 'jersey_number' => 22, 'position' => 'RB', 'birth_date' => '1998-04-04', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 175, 'weight' => 72],
            ['team' => 'Trabzonspor', 'first_name' => 'Okay', 'last_name' => 'Yokuşlu', 'jersey_number' => 6, 'position' => 'CDM', 'birth_date' => '1994-03-09', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 189, 'weight' => 80],

            // ===== Samsunspor (2025-2026) =====
            ['team' => 'Samsunspor', 'first_name' => 'Okan', 'last_name' => 'Kocuk', 'jersey_number' => 25, 'position' => 'GK', 'birth_date' => '1995-07-27', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 190, 'weight' => 82],
            ['team' => 'Samsunspor', 'first_name' => 'Logi', 'last_name' => 'Tomasson', 'jersey_number' => 17, 'position' => 'LW', 'birth_date' => '1996-09-04', 'dominant_foot' => 'right', 'nationality' => 'İzlanda', 'height' => 174, 'weight' => 70],
            ['team' => 'Samsunspor', 'first_name' => 'Carlo', 'last_name' => 'Holse', 'jersey_number' => 8, 'position' => 'RW', 'birth_date' => '1999-06-25', 'dominant_foot' => 'right', 'nationality' => 'Danimarka', 'height' => 177, 'weight' => 72],
            ['team' => 'Samsunspor', 'first_name' => 'Antoine', 'last_name' => 'Makoumbou', 'jersey_number' => 6, 'position' => 'CDM', 'birth_date' => '1998-06-18', 'dominant_foot' => 'right', 'nationality' => 'Kongo', 'height' => 184, 'weight' => 77],
            ['team' => 'Samsunspor', 'first_name' => 'Soner', 'last_name' => 'Gönül', 'jersey_number' => 3, 'position' => 'LB', 'birth_date' => '2000-08-12', 'dominant_foot' => 'left', 'nationality' => 'Türkiye', 'height' => 175, 'weight' => 70],
            ['team' => 'Samsunspor', 'first_name' => 'Zeki', 'last_name' => 'Yavru', 'jersey_number' => 23, 'position' => 'RB', 'birth_date' => '1992-07-22', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 175, 'weight' => 70],
            ['team' => 'Samsunspor', 'first_name' => 'Toni', 'last_name' => 'Borevković', 'jersey_number' => 4, 'position' => 'CB', 'birth_date' => '1997-09-15', 'dominant_foot' => 'right', 'nationality' => 'Hırvatistan', 'height' => 188, 'weight' => 82],
            ['team' => 'Samsunspor', 'first_name' => 'Rick', 'last_name' => 'van Drongelen', 'jersey_number' => 5, 'position' => 'CB', 'birth_date' => '1998-12-20', 'dominant_foot' => 'left', 'nationality' => 'Hollanda', 'height' => 188, 'weight' => 80],
            ['team' => 'Samsunspor', 'first_name' => 'Olivier', 'last_name' => 'Ntcham', 'jersey_number' => 18, 'position' => 'CM', 'birth_date' => '1996-02-09', 'dominant_foot' => 'right', 'nationality' => 'Fransa', 'height' => 178, 'weight' => 72],
            ['team' => 'Samsunspor', 'first_name' => 'Marius', 'last_name' => 'Mouandilmadji', 'jersey_number' => 9, 'position' => 'ST', 'birth_date' => '1998-04-08', 'dominant_foot' => 'right', 'nationality' => 'Çad', 'height' => 186, 'weight' => 79],
            ['team' => 'Samsunspor', 'first_name' => 'Ercan', 'last_name' => 'Kara', 'jersey_number' => 11, 'position' => 'ST', 'birth_date' => '1995-12-03', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 192, 'weight' => 85],

            // ===== Göztepe (2025-2026) =====
            ['team' => 'Göztepe', 'first_name' => 'Mateusz', 'last_name' => 'Lis', 'jersey_number' => 1, 'position' => 'GK', 'birth_date' => '1997-02-26', 'dominant_foot' => 'right', 'nationality' => 'Polonya', 'height' => 194, 'weight' => 85],
            ['team' => 'Göztepe', 'first_name' => 'Romulo', 'last_name' => 'Cardoso', 'jersey_number' => 9, 'position' => 'ST', 'birth_date' => '1997-09-25', 'dominant_foot' => 'right', 'nationality' => 'Brezilya', 'height' => 186, 'weight' => 80],
            ['team' => 'Göztepe', 'first_name' => 'Dennis', 'last_name' => 'Bouanga', 'jersey_number' => 10, 'position' => 'LW', 'birth_date' => '1994-11-04', 'dominant_foot' => 'right', 'nationality' => 'Gabon', 'height' => 175, 'weight' => 72],
            ['team' => 'Göztepe', 'first_name' => 'Ahmet', 'last_name' => 'Kutucu', 'jersey_number' => 11, 'position' => 'RW', 'birth_date' => '1999-03-01', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 184, 'weight' => 77],
            ['team' => 'Göztepe', 'first_name' => 'Ibrahim', 'last_name' => 'Sabra', 'jersey_number' => 8, 'position' => 'CM', 'birth_date' => '2000-05-12', 'dominant_foot' => 'right', 'nationality' => 'Tunus', 'height' => 180, 'weight' => 73],
            ['team' => 'Göztepe', 'first_name' => 'Olaitan', 'last_name' => 'Olaoluwa', 'jersey_number' => 17, 'position' => 'CDM', 'birth_date' => '1999-04-04', 'dominant_foot' => 'right', 'nationality' => 'Nijerya', 'height' => 183, 'weight' => 75],
            ['team' => 'Göztepe', 'first_name' => 'Malcom', 'last_name' => 'Bokele', 'jersey_number' => 4, 'position' => 'CB', 'birth_date' => '2000-10-15', 'dominant_foot' => 'right', 'nationality' => 'Kamerun', 'height' => 186, 'weight' => 79],
            ['team' => 'Göztepe', 'first_name' => 'Anthony', 'last_name' => 'Dennis', 'jersey_number' => 5, 'position' => 'CB', 'birth_date' => '1999-08-08', 'dominant_foot' => 'left', 'nationality' => 'Nijerya', 'height' => 188, 'weight' => 82],
            ['team' => 'Göztepe', 'first_name' => 'Berkan', 'last_name' => 'Kutlu', 'jersey_number' => 7, 'position' => 'CAM', 'birth_date' => '1998-01-25', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 177, 'weight' => 71],
            ['team' => 'Göztepe', 'first_name' => 'Burak', 'last_name' => 'Yılmaz', 'jersey_number' => 2, 'position' => 'RB', 'birth_date' => '1999-12-12', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 176, 'weight' => 70],
            ['team' => 'Göztepe', 'first_name' => 'Onur', 'last_name' => 'Atasayar', 'jersey_number' => 3, 'position' => 'LB', 'birth_date' => '1995-07-22', 'dominant_foot' => 'left', 'nationality' => 'Türkiye', 'height' => 182, 'weight' => 76],

            // ===== Başakşehir (2025-2026) =====
            ['team' => 'Başakşehir', 'first_name' => 'Muhammed', 'last_name' => 'Şengezer', 'jersey_number' => 1, 'position' => 'GK', 'birth_date' => '1997-03-10', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 188, 'weight' => 80],
            ['team' => 'Başakşehir', 'first_name' => 'Krzysztof', 'last_name' => 'Piątek', 'jersey_number' => 9, 'position' => 'ST', 'birth_date' => '1995-07-01', 'dominant_foot' => 'right', 'nationality' => 'Polonya', 'height' => 183, 'weight' => 78],
            ['team' => 'Başakşehir', 'first_name' => 'Berkay', 'last_name' => 'Özcan', 'jersey_number' => 6, 'position' => 'CM', 'birth_date' => '1998-01-14', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 180, 'weight' => 74],
            ['team' => 'Başakşehir', 'first_name' => 'Davie', 'last_name' => 'Selke', 'jersey_number' => 19, 'position' => 'ST', 'birth_date' => '1995-01-20', 'dominant_foot' => 'right', 'nationality' => 'Almanya', 'height' => 192, 'weight' => 84],
            ['team' => 'Başakşehir', 'first_name' => 'Léo', 'last_name' => 'Duarte', 'jersey_number' => 4, 'position' => 'CB', 'birth_date' => '1996-07-17', 'dominant_foot' => 'right', 'nationality' => 'Brezilya', 'height' => 186, 'weight' => 79],
            ['team' => 'Başakşehir', 'first_name' => 'Onur', 'last_name' => 'Bulut', 'jersey_number' => 22, 'position' => 'RB', 'birth_date' => '1994-04-16', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 179, 'weight' => 76],
            ['team' => 'Başakşehir', 'first_name' => 'Mahmut', 'last_name' => 'Tekdemir', 'jersey_number' => 8, 'position' => 'CDM', 'birth_date' => '1988-01-20', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 183, 'weight' => 78],
            ['team' => 'Başakşehir', 'first_name' => 'Deniz', 'last_name' => 'Türüç', 'jersey_number' => 7, 'position' => 'RW', 'birth_date' => '1993-01-23', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 183, 'weight' => 76],
            ['team' => 'Başakşehir', 'first_name' => 'Eldor', 'last_name' => 'Shomurodov', 'jersey_number' => 11, 'position' => 'ST', 'birth_date' => '1995-06-29', 'dominant_foot' => 'right', 'nationality' => 'Özbekistan', 'height' => 191, 'weight' => 82],
            ['team' => 'Başakşehir', 'first_name' => 'Yusuf', 'last_name' => 'Sarı', 'jersey_number' => 21, 'position' => 'LW', 'birth_date' => '1998-09-12', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 175, 'weight' => 68],
            ['team' => 'Başakşehir', 'first_name' => 'Ömer', 'last_name' => 'Ali Şahiner', 'jersey_number' => 10, 'position' => 'CAM', 'birth_date' => '1996-08-02', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 177, 'weight' => 70],

            // ===== Antalyaspor (2025-2026) =====
            ['team' => 'Antalyaspor', 'first_name' => 'Julian', 'last_name' => 'Pollersbeck', 'jersey_number' => 1, 'position' => 'GK', 'birth_date' => '1994-08-16', 'dominant_foot' => 'right', 'nationality' => 'Almanya', 'height' => 192, 'weight' => 85],
            ['team' => 'Antalyaspor', 'first_name' => 'Sander', 'last_name' => 'van de Streek', 'jersey_number' => 8, 'position' => 'CM', 'birth_date' => '1992-11-18', 'dominant_foot' => 'right', 'nationality' => 'Hollanda', 'height' => 184, 'weight' => 78],
            ['team' => 'Antalyaspor', 'first_name' => 'Sam', 'last_name' => 'Larsson', 'jersey_number' => 18, 'position' => 'RW', 'birth_date' => '1993-04-10', 'dominant_foot' => 'left', 'nationality' => 'İsveç', 'height' => 176, 'weight' => 71],
            ['team' => 'Antalyaspor', 'first_name' => 'Bünyamin', 'last_name' => 'Balcı', 'jersey_number' => 20, 'position' => 'LB', 'birth_date' => '1995-09-10', 'dominant_foot' => 'left', 'nationality' => 'Türkiye', 'height' => 180, 'weight' => 74],
            ['team' => 'Antalyaspor', 'first_name' => 'Soner', 'last_name' => 'Aydoğdu', 'jersey_number' => 21, 'position' => 'CAM', 'birth_date' => '1991-11-05', 'dominant_foot' => 'left', 'nationality' => 'Türkiye', 'height' => 175, 'weight' => 70],
            ['team' => 'Antalyaspor', 'first_name' => 'Adams', 'last_name' => 'Gueye', 'jersey_number' => 11, 'position' => 'ST', 'birth_date' => '1998-05-22', 'dominant_foot' => 'right', 'nationality' => 'Senegal', 'height' => 184, 'weight' => 78],
            ['team' => 'Antalyaspor', 'first_name' => 'Cebrail', 'last_name' => 'Karayel', 'jersey_number' => 5, 'position' => 'CB', 'birth_date' => '1996-02-16', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 185, 'weight' => 78],
            ['team' => 'Antalyaspor', 'first_name' => 'Veysel', 'last_name' => 'Sarı', 'jersey_number' => 4, 'position' => 'CB', 'birth_date' => '1988-07-25', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 184, 'weight' => 79],
            ['team' => 'Antalyaspor', 'first_name' => 'Yiğit', 'last_name' => 'Gökoğlan', 'jersey_number' => 28, 'position' => 'RB', 'birth_date' => '1991-08-25', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 174, 'weight' => 69],
            ['team' => 'Antalyaspor', 'first_name' => 'Doğukan', 'last_name' => 'Sinik', 'jersey_number' => 17, 'position' => 'LW', 'birth_date' => '1999-04-05', 'dominant_foot' => 'left', 'nationality' => 'Türkiye', 'height' => 178, 'weight' => 73],
            ['team' => 'Antalyaspor', 'first_name' => 'Boli', 'last_name' => 'Bolingoli', 'jersey_number' => 13, 'position' => 'CDM', 'birth_date' => '1995-07-01', 'dominant_foot' => 'left', 'nationality' => 'Belçika', 'height' => 180, 'weight' => 75],

            // ===== Konyaspor (2025-2026) =====
            ['team' => 'Konyaspor', 'first_name' => 'Bahadır', 'last_name' => 'Güngördü', 'jersey_number' => 25, 'position' => 'GK', 'birth_date' => '1992-08-01', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 188, 'weight' => 81],
            ['team' => 'Konyaspor', 'first_name' => 'Calusic', 'last_name' => 'Robert', 'jersey_number' => 4, 'position' => 'CB', 'birth_date' => '1995-08-25', 'dominant_foot' => 'right', 'nationality' => 'Hırvatistan', 'height' => 186, 'weight' => 80],
            ['team' => 'Konyaspor', 'first_name' => 'Adil', 'last_name' => 'Demirbağ', 'jersey_number' => 3, 'position' => 'LB', 'birth_date' => '1996-08-15', 'dominant_foot' => 'left', 'nationality' => 'Türkiye', 'height' => 182, 'weight' => 75],
            ['team' => 'Konyaspor', 'first_name' => 'Guilherme', 'last_name' => 'Pato', 'jersey_number' => 10, 'position' => 'CAM', 'birth_date' => '1995-09-13', 'dominant_foot' => 'right', 'nationality' => 'Brezilya', 'height' => 173, 'weight' => 70],
            ['team' => 'Konyaspor', 'first_name' => 'Andraž', 'last_name' => 'Šporar', 'jersey_number' => 9, 'position' => 'ST', 'birth_date' => '1994-02-27', 'dominant_foot' => 'right', 'nationality' => 'Slovenya', 'height' => 184, 'weight' => 80],
            ['team' => 'Konyaspor', 'first_name' => 'Endrick', 'last_name' => 'Tetteh', 'jersey_number' => 7, 'position' => 'RW', 'birth_date' => '2001-03-04', 'dominant_foot' => 'left', 'nationality' => 'Gana', 'height' => 176, 'weight' => 72],
            ['team' => 'Konyaspor', 'first_name' => 'Marko', 'last_name' => 'Bakić', 'jersey_number' => 8, 'position' => 'CDM', 'birth_date' => '1993-11-01', 'dominant_foot' => 'right', 'nationality' => 'Karadağ', 'height' => 184, 'weight' => 78],
            ['team' => 'Konyaspor', 'first_name' => 'Soner', 'last_name' => 'Dikmen', 'jersey_number' => 6, 'position' => 'CM', 'birth_date' => '1995-01-02', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 178, 'weight' => 73],
            ['team' => 'Konyaspor', 'first_name' => 'Melih', 'last_name' => 'Bostan', 'jersey_number' => 2, 'position' => 'RB', 'birth_date' => '1998-09-19', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 178, 'weight' => 72],
            ['team' => 'Konyaspor', 'first_name' => 'Riad', 'last_name' => 'Bajic', 'jersey_number' => 11, 'position' => 'ST', 'birth_date' => '1994-05-06', 'dominant_foot' => 'right', 'nationality' => 'Bosna Hersek', 'height' => 191, 'weight' => 84],
            ['team' => 'Konyaspor', 'first_name' => 'Adam', 'last_name' => 'Bareiro', 'jersey_number' => 17, 'position' => 'LW', 'birth_date' => '1996-12-13', 'dominant_foot' => 'right', 'nationality' => 'Paraguay', 'height' => 175, 'weight' => 71],

            // ===== Eyüpspor (2025-2026) =====
            ['team' => 'Eyüpspor', 'first_name' => 'Felipe', 'last_name' => 'Aguilar', 'jersey_number' => 4, 'position' => 'CB', 'birth_date' => '1993-03-23', 'dominant_foot' => 'right', 'nationality' => 'Kolombiya', 'height' => 186, 'weight' => 80],
            ['team' => 'Eyüpspor', 'first_name' => 'Mateusz', 'last_name' => 'Lis', 'jersey_number' => 1, 'position' => 'GK', 'birth_date' => '1997-02-26', 'dominant_foot' => 'right', 'nationality' => 'Polonya', 'height' => 194, 'weight' => 85],
            ['team' => 'Eyüpspor', 'first_name' => 'Halil', 'last_name' => 'Akbunar', 'jersey_number' => 11, 'position' => 'LW', 'birth_date' => '1993-12-26', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 174, 'weight' => 70],
            ['team' => 'Eyüpspor', 'first_name' => 'Robin', 'last_name' => 'Yalçın', 'jersey_number' => 5, 'position' => 'CB', 'birth_date' => '1999-12-04', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 186, 'weight' => 80],
            ['team' => 'Eyüpspor', 'first_name' => 'Metehan', 'last_name' => 'Mimaroğlu', 'jersey_number' => 7, 'position' => 'CM', 'birth_date' => '1999-08-10', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 175, 'weight' => 70],
            ['team' => 'Eyüpspor', 'first_name' => 'Talha', 'last_name' => 'Ülvan', 'jersey_number' => 9, 'position' => 'ST', 'birth_date' => '1996-08-10', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 184, 'weight' => 78],
            ['team' => 'Eyüpspor', 'first_name' => 'Mateo', 'last_name' => 'Susic', 'jersey_number' => 6, 'position' => 'CDM', 'birth_date' => '1990-05-03', 'dominant_foot' => 'right', 'nationality' => 'Bosna Hersek', 'height' => 184, 'weight' => 77],
            ['team' => 'Eyüpspor', 'first_name' => 'Anıl', 'last_name' => 'Karaer', 'jersey_number' => 3, 'position' => 'LB', 'birth_date' => '1995-01-04', 'dominant_foot' => 'left', 'nationality' => 'Türkiye', 'height' => 181, 'weight' => 75],
            ['team' => 'Eyüpspor', 'first_name' => 'Umut', 'last_name' => 'Meraş', 'jersey_number' => 2, 'position' => 'RB', 'birth_date' => '1995-02-20', 'dominant_foot' => 'left', 'nationality' => 'Türkiye', 'height' => 178, 'weight' => 72],
            ['team' => 'Eyüpspor', 'first_name' => 'Berkan', 'last_name' => 'Emir', 'jersey_number' => 10, 'position' => 'CAM', 'birth_date' => '1994-05-08', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 175, 'weight' => 70],
            ['team' => 'Eyüpspor', 'first_name' => 'Léo', 'last_name' => 'Bonatini', 'jersey_number' => 19, 'position' => 'RW', 'birth_date' => '1994-03-28', 'dominant_foot' => 'right', 'nationality' => 'Brezilya', 'height' => 182, 'weight' => 75],

            // ===== Kasımpaşa (2025-2026) =====
            ['team' => 'Kasımpaşa', 'first_name' => 'Ertuğrul', 'last_name' => 'Taşkıran', 'jersey_number' => 1, 'position' => 'GK', 'birth_date' => '1996-06-10', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 185, 'weight' => 78],
            ['team' => 'Kasımpaşa', 'first_name' => 'Aytaç', 'last_name' => 'Kara', 'jersey_number' => 8, 'position' => 'CDM', 'birth_date' => '1993-01-06', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 184, 'weight' => 76],
            ['team' => 'Kasımpaşa', 'first_name' => 'Haris', 'last_name' => 'Hajradinović', 'jersey_number' => 10, 'position' => 'CAM', 'birth_date' => '1994-06-16', 'dominant_foot' => 'left', 'nationality' => 'Bosna Hersek', 'height' => 182, 'weight' => 77],
            ['team' => 'Kasımpaşa', 'first_name' => 'Cafu', 'last_name' => 'Phete', 'jersey_number' => 4, 'position' => 'CB', 'birth_date' => '1995-10-04', 'dominant_foot' => 'right', 'nationality' => 'Güney Afrika', 'height' => 186, 'weight' => 81],
            ['team' => 'Kasımpaşa', 'first_name' => 'Florent', 'last_name' => 'Indalecio', 'jersey_number' => 17, 'position' => 'CM', 'birth_date' => '1995-09-12', 'dominant_foot' => 'right', 'nationality' => 'Madagaskar', 'height' => 178, 'weight' => 72],
            ['team' => 'Kasımpaşa', 'first_name' => 'Cas', 'last_name' => 'Crucet', 'jersey_number' => 5, 'position' => 'CB', 'birth_date' => '2001-10-22', 'dominant_foot' => 'right', 'nationality' => 'Hollanda', 'height' => 188, 'weight' => 82],
            ['team' => 'Kasımpaşa', 'first_name' => 'Mustapha', 'last_name' => 'Yatabaré', 'jersey_number' => 9, 'position' => 'ST', 'birth_date' => '1986-01-26', 'dominant_foot' => 'right', 'nationality' => 'Mali', 'height' => 188, 'weight' => 82],
            ['team' => 'Kasımpaşa', 'first_name' => 'Fode', 'last_name' => 'Koita', 'jersey_number' => 11, 'position' => 'LW', 'birth_date' => '1991-10-09', 'dominant_foot' => 'right', 'nationality' => 'Gine', 'height' => 184, 'weight' => 79],
            ['team' => 'Kasımpaşa', 'first_name' => 'Tarkan', 'last_name' => 'Serbest', 'jersey_number' => 6, 'position' => 'CDM', 'birth_date' => '1995-03-22', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 182, 'weight' => 76],
            ['team' => 'Kasımpaşa', 'first_name' => 'Adem', 'last_name' => 'Büyük', 'jersey_number' => 7, 'position' => 'RW', 'birth_date' => '1987-08-30', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 181, 'weight' => 74],
            ['team' => 'Kasımpaşa', 'first_name' => 'Mortadha', 'last_name' => 'Ben Ouanes', 'jersey_number' => 2, 'position' => 'RB', 'birth_date' => '1996-11-04', 'dominant_foot' => 'right', 'nationality' => 'Tunus', 'height' => 184, 'weight' => 77],

            // ===== Kayserispor (2025-2026) =====
            ['team' => 'Kayserispor', 'first_name' => 'Bilal', 'last_name' => 'Bayazıt', 'jersey_number' => 1, 'position' => 'GK', 'birth_date' => '1999-09-09', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 188, 'weight' => 81],
            ['team' => 'Kayserispor', 'first_name' => 'Mané', 'last_name' => 'Sangaré', 'jersey_number' => 4, 'position' => 'CB', 'birth_date' => '1994-12-30', 'dominant_foot' => 'right', 'nationality' => 'Fildişi', 'height' => 192, 'weight' => 84],
            ['team' => 'Kayserispor', 'first_name' => 'Maxim', 'last_name' => 'Choupo-Moting', 'jersey_number' => 9, 'position' => 'ST', 'birth_date' => '1989-03-23', 'dominant_foot' => 'right', 'nationality' => 'Kamerun', 'height' => 191, 'weight' => 91],
            ['team' => 'Kayserispor', 'first_name' => 'Burak', 'last_name' => 'Kapacak', 'jersey_number' => 7, 'position' => 'LW', 'birth_date' => '1997-09-09', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 173, 'weight' => 69],
            ['team' => 'Kayserispor', 'first_name' => 'Stefano', 'last_name' => 'Beltrame', 'jersey_number' => 10, 'position' => 'CAM', 'birth_date' => '1993-02-07', 'dominant_foot' => 'right', 'nationality' => 'İtalya', 'height' => 175, 'weight' => 70],
            ['team' => 'Kayserispor', 'first_name' => 'Carlos', 'last_name' => 'Mané', 'jersey_number' => 11, 'position' => 'RW', 'birth_date' => '1994-03-11', 'dominant_foot' => 'left', 'nationality' => 'Portekiz', 'height' => 174, 'weight' => 70],
            ['team' => 'Kayserispor', 'first_name' => 'Bennie', 'last_name' => 'Adekuoroye', 'jersey_number' => 6, 'position' => 'CDM', 'birth_date' => '1996-04-22', 'dominant_foot' => 'right', 'nationality' => 'Nijerya', 'height' => 184, 'weight' => 78],
            ['team' => 'Kayserispor', 'first_name' => 'Joao', 'last_name' => 'Mendes', 'jersey_number' => 5, 'position' => 'CB', 'birth_date' => '1996-04-04', 'dominant_foot' => 'right', 'nationality' => 'Brezilya', 'height' => 186, 'weight' => 79],
            ['team' => 'Kayserispor', 'first_name' => 'Soner', 'last_name' => 'Aydoğdu', 'jersey_number' => 8, 'position' => 'CM', 'birth_date' => '1991-11-05', 'dominant_foot' => 'left', 'nationality' => 'Türkiye', 'height' => 175, 'weight' => 70],
            ['team' => 'Kayserispor', 'first_name' => 'Onur', 'last_name' => 'Bulut', 'jersey_number' => 2, 'position' => 'RB', 'birth_date' => '1994-04-16', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 179, 'weight' => 76],
            ['team' => 'Kayserispor', 'first_name' => 'Furkan', 'last_name' => 'Soyalp', 'jersey_number' => 3, 'position' => 'LB', 'birth_date' => '1998-09-08', 'dominant_foot' => 'left', 'nationality' => 'Türkiye', 'height' => 176, 'weight' => 70],

            // ===== Gaziantep FK (2025-2026) =====
            ['team' => 'Gaziantep FK', 'first_name' => 'Zafer', 'last_name' => 'Görgen', 'jersey_number' => 1, 'position' => 'GK', 'birth_date' => '1996-05-19', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 191, 'weight' => 84],
            ['team' => 'Gaziantep FK', 'first_name' => 'Furkan', 'last_name' => 'Bayır', 'jersey_number' => 4, 'position' => 'CB', 'birth_date' => '1998-12-22', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 187, 'weight' => 80],
            ['team' => 'Gaziantep FK', 'first_name' => 'Salem', 'last_name' => 'M\'Bakata', 'jersey_number' => 9, 'position' => 'ST', 'birth_date' => '1997-03-04', 'dominant_foot' => 'right', 'nationality' => 'Kongo', 'height' => 188, 'weight' => 82],
            ['team' => 'Gaziantep FK', 'first_name' => 'Maxim', 'last_name' => 'Wanderson', 'jersey_number' => 10, 'position' => 'CAM', 'birth_date' => '1994-03-20', 'dominant_foot' => 'left', 'nationality' => 'Brezilya', 'height' => 177, 'weight' => 72],
            ['team' => 'Gaziantep FK', 'first_name' => 'Anıl', 'last_name' => 'Yiğit', 'jersey_number' => 7, 'position' => 'RW', 'birth_date' => '1998-04-13', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 178, 'weight' => 73],
            ['team' => 'Gaziantep FK', 'first_name' => 'Hugo', 'last_name' => 'Sousa', 'jersey_number' => 5, 'position' => 'CDM', 'birth_date' => '1995-09-25', 'dominant_foot' => 'right', 'nationality' => 'Portekiz', 'height' => 186, 'weight' => 78],
            ['team' => 'Gaziantep FK', 'first_name' => 'Mehmet', 'last_name' => 'Çakır', 'jersey_number' => 3, 'position' => 'LB', 'birth_date' => '1999-01-15', 'dominant_foot' => 'left', 'nationality' => 'Türkiye', 'height' => 178, 'weight' => 72],
            ['team' => 'Gaziantep FK', 'first_name' => 'Kevin', 'last_name' => 'Rodrigues', 'jersey_number' => 6, 'position' => 'CM', 'birth_date' => '1994-03-05', 'dominant_foot' => 'left', 'nationality' => 'Portekiz', 'height' => 178, 'weight' => 73],
            ['team' => 'Gaziantep FK', 'first_name' => 'Junior', 'last_name' => 'Morais', 'jersey_number' => 2, 'position' => 'RB', 'birth_date' => '1986-11-22', 'dominant_foot' => 'right', 'nationality' => 'Romanya', 'height' => 174, 'weight' => 70],
            ['team' => 'Gaziantep FK', 'first_name' => 'Volkan', 'last_name' => 'Şen', 'jersey_number' => 11, 'position' => 'LW', 'birth_date' => '1987-07-07', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 178, 'weight' => 73],
            ['team' => 'Gaziantep FK', 'first_name' => 'Sergen', 'last_name' => 'Yalçın', 'jersey_number' => 8, 'position' => 'CM', 'birth_date' => '1998-09-08', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 180, 'weight' => 74],

            // ===== Alanyaspor (2025-2026) =====
            ['team' => 'Alanyaspor', 'first_name' => 'Ertugrul', 'last_name' => 'Taşkıran', 'jersey_number' => 1, 'position' => 'GK', 'birth_date' => '1996-06-10', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 185, 'weight' => 78],
            ['team' => 'Alanyaspor', 'first_name' => 'Maicon', 'last_name' => 'Roque', 'jersey_number' => 5, 'position' => 'CB', 'birth_date' => '1988-07-14', 'dominant_foot' => 'right', 'nationality' => 'Brezilya', 'height' => 185, 'weight' => 80],
            ['team' => 'Alanyaspor', 'first_name' => 'Efkan', 'last_name' => 'Bekiroğlu', 'jersey_number' => 8, 'position' => 'CM', 'birth_date' => '1995-09-24', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 175, 'weight' => 70],
            ['team' => 'Alanyaspor', 'first_name' => 'Hadži', 'last_name' => 'Krcić', 'jersey_number' => 9, 'position' => 'ST', 'birth_date' => '1996-08-09', 'dominant_foot' => 'right', 'nationality' => 'Bosna Hersek', 'height' => 195, 'weight' => 88],
            ['team' => 'Alanyaspor', 'first_name' => 'Hwang', 'last_name' => 'Ui-jo', 'jersey_number' => 11, 'position' => 'ST', 'birth_date' => '1992-08-28', 'dominant_foot' => 'right', 'nationality' => 'Güney Kore', 'height' => 184, 'weight' => 77],
            ['team' => 'Alanyaspor', 'first_name' => 'Berkan', 'last_name' => 'Kutlu', 'jersey_number' => 6, 'position' => 'CDM', 'birth_date' => '1998-01-25', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 177, 'weight' => 71],
            ['team' => 'Alanyaspor', 'first_name' => 'Furkan', 'last_name' => 'Bekleviç', 'jersey_number' => 4, 'position' => 'CB', 'birth_date' => '1994-03-29', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 186, 'weight' => 80],
            ['team' => 'Alanyaspor', 'first_name' => 'Ümit', 'last_name' => 'Akdağ', 'jersey_number' => 3, 'position' => 'LB', 'birth_date' => '2003-10-19', 'dominant_foot' => 'left', 'nationality' => 'Türkiye', 'height' => 184, 'weight' => 76],
            ['team' => 'Alanyaspor', 'first_name' => 'Fatih', 'last_name' => 'Aksoy', 'jersey_number' => 2, 'position' => 'RB', 'birth_date' => '1997-09-12', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 183, 'weight' => 75],
            ['team' => 'Alanyaspor', 'first_name' => 'Janvier', 'last_name' => 'Asare', 'jersey_number' => 18, 'position' => 'LW', 'birth_date' => '2001-01-13', 'dominant_foot' => 'right', 'nationality' => 'Gana', 'height' => 174, 'weight' => 70],
            ['team' => 'Alanyaspor', 'first_name' => 'Ioannis', 'last_name' => 'Maniatis', 'jersey_number' => 10, 'position' => 'CAM', 'birth_date' => '1986-10-12', 'dominant_foot' => 'right', 'nationality' => 'Yunanistan', 'height' => 180, 'weight' => 75],

            // ===== Çaykur Rizespor (2025-2026) =====
            ['team' => 'Çaykur Rizespor', 'first_name' => 'Erten', 'last_name' => 'Ersu', 'jersey_number' => 1, 'position' => 'GK', 'birth_date' => '1991-04-11', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 191, 'weight' => 83],
            ['team' => 'Çaykur Rizespor', 'first_name' => 'Aliou', 'last_name' => 'Dieng', 'jersey_number' => 6, 'position' => 'CDM', 'birth_date' => '1997-10-09', 'dominant_foot' => 'right', 'nationality' => 'Mali', 'height' => 188, 'weight' => 80],
            ['team' => 'Çaykur Rizespor', 'first_name' => 'Halil', 'last_name' => 'Dervişoğlu', 'jersey_number' => 9, 'position' => 'ST', 'birth_date' => '1999-12-08', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 184, 'weight' => 77],
            ['team' => 'Çaykur Rizespor', 'first_name' => 'Adolfo', 'last_name' => 'Gaich', 'jersey_number' => 11, 'position' => 'ST', 'birth_date' => '1999-02-26', 'dominant_foot' => 'right', 'nationality' => 'Arjantin', 'height' => 193, 'weight' => 85],
            ['team' => 'Çaykur Rizespor', 'first_name' => 'Joel', 'last_name' => 'Pohjanpalo', 'jersey_number' => 17, 'position' => 'ST', 'birth_date' => '1994-09-13', 'dominant_foot' => 'right', 'nationality' => 'Finlandiya', 'height' => 184, 'weight' => 79],
            ['team' => 'Çaykur Rizespor', 'first_name' => 'Mithat', 'last_name' => 'Pala', 'jersey_number' => 4, 'position' => 'CB', 'birth_date' => '1998-08-09', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 188, 'weight' => 82],
            ['team' => 'Çaykur Rizespor', 'first_name' => 'Casper', 'last_name' => 'Højer Nielsen', 'jersey_number' => 2, 'position' => 'RB', 'birth_date' => '1994-03-31', 'dominant_foot' => 'right', 'nationality' => 'Danimarka', 'height' => 181, 'weight' => 75],
            ['team' => 'Çaykur Rizespor', 'first_name' => 'Ali', 'last_name' => 'Sowe', 'jersey_number' => 7, 'position' => 'CAM', 'birth_date' => '1994-06-04', 'dominant_foot' => 'right', 'nationality' => 'Gambiya', 'height' => 184, 'weight' => 75],
            ['team' => 'Çaykur Rizespor', 'first_name' => 'Christopher', 'last_name' => 'Operi', 'jersey_number' => 3, 'position' => 'LB', 'birth_date' => '1997-07-08', 'dominant_foot' => 'left', 'nationality' => 'Fildişi Sahili', 'height' => 184, 'weight' => 77],
            ['team' => 'Çaykur Rizespor', 'first_name' => 'Selim', 'last_name' => 'Dilli', 'jersey_number' => 5, 'position' => 'CB', 'birth_date' => '2003-08-25', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 187, 'weight' => 80],
            ['team' => 'Çaykur Rizespor', 'first_name' => 'Yasin', 'last_name' => 'Pehlivan', 'jersey_number' => 8, 'position' => 'CM', 'birth_date' => '1989-01-05', 'dominant_foot' => 'right', 'nationality' => 'Avusturya', 'height' => 184, 'weight' => 78],

            // ===== Kocaelispor (2025-2026, yeni çıkan) =====
            ['team' => 'Kocaelispor', 'first_name' => 'Şener', 'last_name' => 'Büyükaycan', 'jersey_number' => 1, 'position' => 'GK', 'birth_date' => '1990-11-02', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 191, 'weight' => 84],
            ['team' => 'Kocaelispor', 'first_name' => 'Lirim', 'last_name' => 'Kastrati', 'jersey_number' => 11, 'position' => 'LW', 'birth_date' => '1999-02-12', 'dominant_foot' => 'right', 'nationality' => 'Kosova', 'height' => 178, 'weight' => 72],
            ['team' => 'Kocaelispor', 'first_name' => 'Petar', 'last_name' => 'Bojić', 'jersey_number' => 4, 'position' => 'CB', 'birth_date' => '1998-05-28', 'dominant_foot' => 'right', 'nationality' => 'Sırbistan', 'height' => 192, 'weight' => 85],
            ['team' => 'Kocaelispor', 'first_name' => 'Caner', 'last_name' => 'Erkin', 'jersey_number' => 3, 'position' => 'LB', 'birth_date' => '1988-10-04', 'dominant_foot' => 'left', 'nationality' => 'Türkiye', 'height' => 184, 'weight' => 79],
            ['team' => 'Kocaelispor', 'first_name' => 'Onurcan', 'last_name' => 'Piri', 'jersey_number' => 6, 'position' => 'CDM', 'birth_date' => '2000-06-10', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 182, 'weight' => 75],
            ['team' => 'Kocaelispor', 'first_name' => 'Yalçın', 'last_name' => 'Kayan', 'jersey_number' => 7, 'position' => 'CAM', 'birth_date' => '1996-04-11', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 178, 'weight' => 73],
            ['team' => 'Kocaelispor', 'first_name' => 'Onur', 'last_name' => 'Ergün', 'jersey_number' => 5, 'position' => 'CB', 'birth_date' => '1997-01-15', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 188, 'weight' => 82],
            ['team' => 'Kocaelispor', 'first_name' => 'Emin', 'last_name' => 'Bayram', 'jersey_number' => 2, 'position' => 'RB', 'birth_date' => '2003-08-04', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 180, 'weight' => 74],
            ['team' => 'Kocaelispor', 'first_name' => 'Anel', 'last_name' => 'Ahmedhodžić', 'jersey_number' => 14, 'position' => 'CM', 'birth_date' => '1999-03-26', 'dominant_foot' => 'right', 'nationality' => 'Bosna Hersek', 'height' => 188, 'weight' => 80],
            ['team' => 'Kocaelispor', 'first_name' => 'Mendes', 'last_name' => 'Silva', 'jersey_number' => 9, 'position' => 'ST', 'birth_date' => '1995-07-15', 'dominant_foot' => 'right', 'nationality' => 'Brezilya', 'height' => 186, 'weight' => 80],
            ['team' => 'Kocaelispor', 'first_name' => 'Ahmet', 'last_name' => 'Sağat', 'jersey_number' => 17, 'position' => 'RW', 'birth_date' => '2000-09-18', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 175, 'weight' => 71],

            // ===== Fatih Karagümrük (2025-2026, yeni çıkan) =====
            ['team' => 'Fatih Karagümrük', 'first_name' => 'Volkan', 'last_name' => 'Babacan', 'jersey_number' => 1, 'position' => 'GK', 'birth_date' => '1988-08-11', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 188, 'weight' => 82],
            ['team' => 'Fatih Karagümrük', 'first_name' => 'Fabio', 'last_name' => 'Borini', 'jersey_number' => 11, 'position' => 'ST', 'birth_date' => '1991-03-29', 'dominant_foot' => 'right', 'nationality' => 'İtalya', 'height' => 184, 'weight' => 78],
            ['team' => 'Fatih Karagümrük', 'first_name' => 'Andrea', 'last_name' => 'Belotti', 'jersey_number' => 9, 'position' => 'ST', 'birth_date' => '1993-12-20', 'dominant_foot' => 'right', 'nationality' => 'İtalya', 'height' => 181, 'weight' => 75],
            ['team' => 'Fatih Karagümrük', 'first_name' => 'Mathieu', 'last_name' => 'Valbuena', 'jersey_number' => 10, 'position' => 'CAM', 'birth_date' => '1984-09-28', 'dominant_foot' => 'right', 'nationality' => 'Fransa', 'height' => 167, 'weight' => 62],
            ['team' => 'Fatih Karagümrük', 'first_name' => 'Tiago', 'last_name' => 'Çukur', 'jersey_number' => 7, 'position' => 'LW', 'birth_date' => '2002-09-19', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 184, 'weight' => 76],
            ['team' => 'Fatih Karagümrük', 'first_name' => 'Ricardo', 'last_name' => 'Esgaio', 'jersey_number' => 2, 'position' => 'RB', 'birth_date' => '1993-05-16', 'dominant_foot' => 'right', 'nationality' => 'Portekiz', 'height' => 175, 'weight' => 72],
            ['team' => 'Fatih Karagümrük', 'first_name' => 'Soner', 'last_name' => 'Aydoğdu', 'jersey_number' => 8, 'position' => 'CM', 'birth_date' => '1991-11-05', 'dominant_foot' => 'left', 'nationality' => 'Türkiye', 'height' => 175, 'weight' => 70],
            ['team' => 'Fatih Karagümrük', 'first_name' => 'Ravil', 'last_name' => 'Tagir', 'jersey_number' => 4, 'position' => 'CB', 'birth_date' => '2003-05-04', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 186, 'weight' => 78],
            ['team' => 'Fatih Karagümrük', 'first_name' => 'Markao', 'last_name' => 'Silva', 'jersey_number' => 5, 'position' => 'CB', 'birth_date' => '1993-05-02', 'dominant_foot' => 'right', 'nationality' => 'Brezilya', 'height' => 186, 'weight' => 81],
            ['team' => 'Fatih Karagümrük', 'first_name' => 'Cyriaque', 'last_name' => 'Mayounga', 'jersey_number' => 6, 'position' => 'CDM', 'birth_date' => '1995-06-04', 'dominant_foot' => 'right', 'nationality' => 'Kongo', 'height' => 187, 'weight' => 80],
            ['team' => 'Fatih Karagümrük', 'first_name' => 'Aziz', 'last_name' => 'Kayan', 'jersey_number' => 3, 'position' => 'LB', 'birth_date' => '1990-09-08', 'dominant_foot' => 'left', 'nationality' => 'Türkiye', 'height' => 181, 'weight' => 75],

            // ===== Gençlerbirliği (2025-2026, yeni çıkan) =====
            ['team' => 'Gençlerbirliği', 'first_name' => 'Erdem', 'last_name' => 'Canpolat', 'jersey_number' => 1, 'position' => 'GK', 'birth_date' => '1990-04-13', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 192, 'weight' => 84],
            ['team' => 'Gençlerbirliği', 'first_name' => 'Pedro', 'last_name' => 'Henrique', 'jersey_number' => 9, 'position' => 'ST', 'birth_date' => '1993-04-25', 'dominant_foot' => 'right', 'nationality' => 'Brezilya', 'height' => 183, 'weight' => 77],
            ['team' => 'Gençlerbirliği', 'first_name' => 'Goran', 'last_name' => 'Karačić', 'jersey_number' => 4, 'position' => 'CB', 'birth_date' => '1992-09-09', 'dominant_foot' => 'right', 'nationality' => 'Hırvatistan', 'height' => 186, 'weight' => 81],
            ['team' => 'Gençlerbirliği', 'first_name' => 'Onur', 'last_name' => 'Aydın', 'jersey_number' => 7, 'position' => 'RW', 'birth_date' => '1998-04-15', 'dominant_foot' => 'left', 'nationality' => 'Türkiye', 'height' => 175, 'weight' => 70],
            ['team' => 'Gençlerbirliği', 'first_name' => 'Mehmet', 'last_name' => 'Sıddık', 'jersey_number' => 3, 'position' => 'LB', 'birth_date' => '2001-04-20', 'dominant_foot' => 'left', 'nationality' => 'Türkiye', 'height' => 178, 'weight' => 72],
            ['team' => 'Gençlerbirliği', 'first_name' => 'Hatem', 'last_name' => 'Yıldız', 'jersey_number' => 10, 'position' => 'CAM', 'birth_date' => '1996-12-04', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 176, 'weight' => 70],
            ['team' => 'Gençlerbirliği', 'first_name' => 'Mete', 'last_name' => 'Kaan', 'jersey_number' => 6, 'position' => 'CDM', 'birth_date' => '1999-07-22', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 183, 'weight' => 76],
            ['team' => 'Gençlerbirliği', 'first_name' => 'Berat', 'last_name' => 'Özdemir', 'jersey_number' => 8, 'position' => 'CM', 'birth_date' => '1998-04-20', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 180, 'weight' => 73],
            ['team' => 'Gençlerbirliği', 'first_name' => 'Anıl', 'last_name' => 'Karaer', 'jersey_number' => 5, 'position' => 'CB', 'birth_date' => '1995-01-04', 'dominant_foot' => 'left', 'nationality' => 'Türkiye', 'height' => 181, 'weight' => 75],
            ['team' => 'Gençlerbirliği', 'first_name' => 'Hakan', 'last_name' => 'Çalhanoğlu', 'jersey_number' => 11, 'position' => 'LW', 'birth_date' => '2002-01-14', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 174, 'weight' => 68],
            ['team' => 'Gençlerbirliği', 'first_name' => 'Tarkan', 'last_name' => 'Bulut', 'jersey_number' => 2, 'position' => 'RB', 'birth_date' => '1996-07-04', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 179, 'weight' => 74],
        ];

        foreach ($players as $data) {
            $teamId = $teams[$data['team']] ?? null;
            $positionId = $positions[$data['position']] ?? null;

            if (! $teamId || ! $positionId) {
                continue;
            }

            Players::updateOrCreate(
                [
                    'team_id' => $teamId,
                    'jersey_number' => $data['jersey_number'],
                ],
                [
                    'team_id' => $teamId,
                    'position_id' => $positionId,
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'birth_date' => $data['birth_date'],
                    'jersey_number' => $data['jersey_number'],
                    'height' => $data['height'],
                    'weight' => $data['weight'],
                    'dominant_foot' => $data['dominant_foot'],
                    'nationality' => $data['nationality'],
                    'status' => 'active',
                ]
            );
        }
    }
}
