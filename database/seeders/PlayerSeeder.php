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
        $teams = Teams::pluck('id', 'name');

        $players = [
            // Galatasaray
            ['team' => 'Galatasaray', 'first_name' => 'Fernando', 'last_name' => 'Muslera', 'jersey_number' => 1, 'position' => 'GK', 'birth_date' => '1986-06-16', 'dominant_foot' => 'right', 'nationality' => 'Uruguay', 'height' => 190, 'weight' => 85],
            ['team' => 'Galatasaray', 'first_name' => 'Davinson', 'last_name' => 'Sánchez', 'jersey_number' => 2, 'position' => 'CB', 'birth_date' => '1996-06-12', 'dominant_foot' => 'right', 'nationality' => 'Kolombiya', 'height' => 187, 'weight' => 82],
            ['team' => 'Galatasaray', 'first_name' => 'Angeliño', 'last_name' => 'Tasende', 'jersey_number' => 3, 'position' => 'LB', 'birth_date' => '1997-01-04', 'dominant_foot' => 'left', 'nationality' => 'İspanya', 'height' => 175, 'weight' => 68],
            ['team' => 'Galatasaray', 'first_name' => 'Victor', 'last_name' => 'Nelsson', 'jersey_number' => 4, 'position' => 'CB', 'birth_date' => '1999-10-14', 'dominant_foot' => 'right', 'nationality' => 'Danimarka', 'height' => 186, 'weight' => 78],
            ['team' => 'Galatasaray', 'first_name' => 'Lucas', 'last_name' => 'Torreira', 'jersey_number' => 34, 'position' => 'CDM', 'birth_date' => '1996-02-11', 'dominant_foot' => 'right', 'nationality' => 'Uruguay', 'height' => 168, 'weight' => 65],
            ['team' => 'Galatasaray', 'first_name' => 'Kerem', 'last_name' => 'Aktürkoğlu', 'jersey_number' => 7, 'position' => 'LW', 'birth_date' => '1998-10-21', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 178, 'weight' => 72],
            ['team' => 'Galatasaray', 'first_name' => 'Barış Alper', 'last_name' => 'Yılmaz', 'jersey_number' => 17, 'position' => 'RW', 'birth_date' => '2000-02-23', 'dominant_foot' => 'left', 'nationality' => 'Türkiye', 'height' => 172, 'weight' => 66],
            ['team' => 'Galatasaray', 'first_name' => 'Dries', 'last_name' => 'Mertens', 'jersey_number' => 10, 'position' => 'CAM', 'birth_date' => '1987-05-06', 'dominant_foot' => 'left', 'nationality' => 'Belçika', 'height' => 169, 'weight' => 61],
            ['team' => 'Galatasaray', 'first_name' => 'Mauro', 'last_name' => 'Icardi', 'jersey_number' => 9, 'position' => 'ST', 'birth_date' => '1993-02-19', 'dominant_foot' => 'right', 'nationality' => 'Arjantin', 'height' => 181, 'weight' => 75],
            ['team' => 'Galatasaray', 'first_name' => 'Sacha', 'last_name' => 'Boey', 'jersey_number' => 20, 'position' => 'RB', 'birth_date' => '2000-09-13', 'dominant_foot' => 'right', 'nationality' => 'Fransa', 'height' => 180, 'weight' => 73],
            ['team' => 'Galatasaray', 'first_name' => 'Kaan', 'last_name' => 'Ayhan', 'jersey_number' => 5, 'position' => 'CB', 'birth_date' => '1994-11-10', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 184, 'weight' => 78],

            // Fenerbahçe
            ['team' => 'Fenerbahçe', 'first_name' => 'Dominik', 'last_name' => 'Livaković', 'jersey_number' => 1, 'position' => 'GK', 'birth_date' => '1995-01-09', 'dominant_foot' => 'right', 'nationality' => 'Hırvatistan', 'height' => 188, 'weight' => 82],
            ['team' => 'Fenerbahçe', 'first_name' => 'Çağlar', 'last_name' => 'Söyüncü', 'jersey_number' => 4, 'position' => 'CB', 'birth_date' => '1996-05-23', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 187, 'weight' => 82],
            ['team' => 'Fenerbahçe', 'first_name' => 'Ferdi', 'last_name' => 'Kadıoğlu', 'jersey_number' => 3, 'position' => 'LB', 'birth_date' => '1999-10-07', 'dominant_foot' => 'left', 'nationality' => 'Türkiye', 'height' => 177, 'weight' => 70],
            ['team' => 'Fenerbahçe', 'first_name' => 'İsmail', 'last_name' => 'Yüksek', 'jersey_number' => 5, 'position' => 'CDM', 'birth_date' => '2000-04-01', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 180, 'weight' => 73],
            ['team' => 'Fenerbahçe', 'first_name' => 'Fred', 'last_name' => 'Rodrigues', 'jersey_number' => 17, 'position' => 'CM', 'birth_date' => '1993-03-05', 'dominant_foot' => 'right', 'nationality' => 'Brezilya', 'height' => 169, 'weight' => 64],
            ['team' => 'Fenerbahçe', 'first_name' => 'Dusan', 'last_name' => 'Tadić', 'jersey_number' => 10, 'position' => 'CAM', 'birth_date' => '1988-11-20', 'dominant_foot' => 'left', 'nationality' => 'Sırbistan', 'height' => 181, 'weight' => 80],
            ['team' => 'Fenerbahçe', 'first_name' => 'Edin', 'last_name' => 'Džeko', 'jersey_number' => 9, 'position' => 'ST', 'birth_date' => '1986-03-17', 'dominant_foot' => 'right', 'nationality' => 'Bosna Hersek', 'height' => 193, 'weight' => 85],
            ['team' => 'Fenerbahçe', 'first_name' => 'Sebastian', 'last_name' => 'Szymański', 'jersey_number' => 22, 'position' => 'RW', 'birth_date' => '1999-05-10', 'dominant_foot' => 'left', 'nationality' => 'Polonya', 'height' => 175, 'weight' => 68],
            ['team' => 'Fenerbahçe', 'first_name' => 'Jayden', 'last_name' => 'Oosterwolde', 'jersey_number' => 12, 'position' => 'LB', 'birth_date' => '2000-08-09', 'dominant_foot' => 'left', 'nationality' => 'Hollanda', 'height' => 185, 'weight' => 76],
            ['team' => 'Fenerbahçe', 'first_name' => 'Bright', 'last_name' => 'Osayi-Samuel', 'jersey_number' => 88, 'position' => 'RB', 'birth_date' => '1997-12-31', 'dominant_foot' => 'right', 'nationality' => 'Nijerya', 'height' => 179, 'weight' => 73],
            ['team' => 'Fenerbahçe', 'first_name' => 'Alexander', 'last_name' => 'Djiku', 'jersey_number' => 15, 'position' => 'CB', 'birth_date' => '1994-08-09', 'dominant_foot' => 'right', 'nationality' => 'Gana', 'height' => 186, 'weight' => 81],

            // Beşiktaş
            ['team' => 'Beşiktaş', 'first_name' => 'Ersin', 'last_name' => 'Destanoğlu', 'jersey_number' => 1, 'position' => 'GK', 'birth_date' => '2000-08-01', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 190, 'weight' => 82],
            ['team' => 'Beşiktaş', 'first_name' => 'Rafa', 'last_name' => 'Silva', 'jersey_number' => 10, 'position' => 'RW', 'birth_date' => '1993-05-17', 'dominant_foot' => 'left', 'nationality' => 'Portekiz', 'height' => 174, 'weight' => 66],
            ['team' => 'Beşiktaş', 'first_name' => 'Cenk', 'last_name' => 'Tosun', 'jersey_number' => 9, 'position' => 'ST', 'birth_date' => '1991-06-07', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 183, 'weight' => 78],
            ['team' => 'Beşiktaş', 'first_name' => 'Gedson', 'last_name' => 'Fernandes', 'jersey_number' => 7, 'position' => 'CM', 'birth_date' => '1999-01-09', 'dominant_foot' => 'right', 'nationality' => 'Portekiz', 'height' => 184, 'weight' => 76],
            ['team' => 'Beşiktaş', 'first_name' => 'Tayyip Talha', 'last_name' => 'Sanuç', 'jersey_number' => 21, 'position' => 'CB', 'birth_date' => '2001-03-15', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 192, 'weight' => 85],
            ['team' => 'Beşiktaş', 'first_name' => 'Mert', 'last_name' => 'Günok', 'jersey_number' => 25, 'position' => 'GK', 'birth_date' => '1989-03-01', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 194, 'weight' => 86],
            ['team' => 'Beşiktaş', 'first_name' => 'Arthur', 'last_name' => 'Masuaku', 'jersey_number' => 3, 'position' => 'LB', 'birth_date' => '1993-11-07', 'dominant_foot' => 'left', 'nationality' => 'Kongo', 'height' => 179, 'weight' => 73],
            ['team' => 'Beşiktaş', 'first_name' => 'Jonas', 'last_name' => 'Svensson', 'jersey_number' => 2, 'position' => 'RB', 'birth_date' => '1993-03-06', 'dominant_foot' => 'right', 'nationality' => 'Norveç', 'height' => 180, 'weight' => 74],
            ['team' => 'Beşiktaş', 'first_name' => 'Ante', 'last_name' => 'Rebić', 'jersey_number' => 18, 'position' => 'LW', 'birth_date' => '1993-09-21', 'dominant_foot' => 'right', 'nationality' => 'Hırvatistan', 'height' => 185, 'weight' => 78],
            ['team' => 'Beşiktaş', 'first_name' => 'Salih', 'last_name' => 'Uçan', 'jersey_number' => 8, 'position' => 'CAM', 'birth_date' => '1994-01-06', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 176, 'weight' => 68],
            ['team' => 'Beşiktaş', 'first_name' => 'Joe', 'last_name' => 'Worrall', 'jersey_number' => 4, 'position' => 'CB', 'birth_date' => '1997-01-10', 'dominant_foot' => 'right', 'nationality' => 'İngiltere', 'height' => 187, 'weight' => 83],

            // Trabzonspor
            ['team' => 'Trabzonspor', 'first_name' => 'Uğurcan', 'last_name' => 'Çakır', 'jersey_number' => 1, 'position' => 'GK', 'birth_date' => '1996-04-05', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 191, 'weight' => 80],
            ['team' => 'Trabzonspor', 'first_name' => 'Trezeguet', 'last_name' => 'Hassan', 'jersey_number' => 17, 'position' => 'RW', 'birth_date' => '1994-10-01', 'dominant_foot' => 'left', 'nationality' => 'Mısır', 'height' => 177, 'weight' => 71],
            ['team' => 'Trabzonspor', 'first_name' => 'Enis', 'last_name' => 'Bardhi', 'jersey_number' => 10, 'position' => 'CAM', 'birth_date' => '1995-07-02', 'dominant_foot' => 'left', 'nationality' => 'Kuzey Makedonya', 'height' => 183, 'weight' => 78],
            ['team' => 'Trabzonspor', 'first_name' => 'Paul', 'last_name' => 'Onuachu', 'jersey_number' => 9, 'position' => 'ST', 'birth_date' => '1994-05-28', 'dominant_foot' => 'right', 'nationality' => 'Nijerya', 'height' => 201, 'weight' => 90],
            ['team' => 'Trabzonspor', 'first_name' => 'Okay', 'last_name' => 'Yokuşlu', 'jersey_number' => 6, 'position' => 'CDM', 'birth_date' => '1994-03-09', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 189, 'weight' => 80],
            ['team' => 'Trabzonspor', 'first_name' => 'Stefano', 'last_name' => 'Denswil', 'jersey_number' => 5, 'position' => 'CB', 'birth_date' => '1993-05-07', 'dominant_foot' => 'left', 'nationality' => 'Hollanda', 'height' => 185, 'weight' => 80],
            ['team' => 'Trabzonspor', 'first_name' => 'Marc', 'last_name' => 'Bartra', 'jersey_number' => 4, 'position' => 'CB', 'birth_date' => '1991-01-15', 'dominant_foot' => 'right', 'nationality' => 'İspanya', 'height' => 184, 'weight' => 77],
            ['team' => 'Trabzonspor', 'first_name' => 'Umut', 'last_name' => 'Bozok', 'jersey_number' => 11, 'position' => 'ST', 'birth_date' => '1996-11-18', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 184, 'weight' => 77],
            ['team' => 'Trabzonspor', 'first_name' => 'Cham', 'last_name' => 'Saracevic', 'jersey_number' => 8, 'position' => 'CM', 'birth_date' => '1998-09-28', 'dominant_foot' => 'right', 'nationality' => 'Avusturya', 'height' => 182, 'weight' => 75],
            ['team' => 'Trabzonspor', 'first_name' => 'Bruno', 'last_name' => 'Peres', 'jersey_number' => 2, 'position' => 'RB', 'birth_date' => '1990-03-01', 'dominant_foot' => 'right', 'nationality' => 'Brezilya', 'height' => 182, 'weight' => 76],

            // Başakşehir
            ['team' => 'Başakşehir', 'first_name' => 'Muhammed', 'last_name' => 'Şengezer', 'jersey_number' => 1, 'position' => 'GK', 'birth_date' => '1997-03-10', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 188, 'weight' => 80],
            ['team' => 'Başakşehir', 'first_name' => 'Serdar', 'last_name' => 'Gürler', 'jersey_number' => 11, 'position' => 'LW', 'birth_date' => '1991-09-20', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 176, 'weight' => 71],
            ['team' => 'Başakşehir', 'first_name' => 'Berkay', 'last_name' => 'Özcan', 'jersey_number' => 6, 'position' => 'CM', 'birth_date' => '1998-01-14', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 180, 'weight' => 74],
            ['team' => 'Başakşehir', 'first_name' => 'Krzysztof', 'last_name' => 'Piątek', 'jersey_number' => 9, 'position' => 'ST', 'birth_date' => '1995-07-01', 'dominant_foot' => 'right', 'nationality' => 'Polonya', 'height' => 183, 'weight' => 78],
            ['team' => 'Başakşehir', 'first_name' => 'Léo', 'last_name' => 'Duarte', 'jersey_number' => 4, 'position' => 'CB', 'birth_date' => '1996-07-17', 'dominant_foot' => 'right', 'nationality' => 'Brezilya', 'height' => 186, 'weight' => 79],
            ['team' => 'Başakşehir', 'first_name' => 'Deniz', 'last_name' => 'Türüç', 'jersey_number' => 7, 'position' => 'RW', 'birth_date' => '1993-01-23', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 183, 'weight' => 76],
            ['team' => 'Başakşehir', 'first_name' => 'Mahmut', 'last_name' => 'Tekdemir', 'jersey_number' => 8, 'position' => 'CDM', 'birth_date' => '1988-01-20', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 183, 'weight' => 78],
            ['team' => 'Başakşehir', 'first_name' => 'Lucas', 'last_name' => 'Lima', 'jersey_number' => 10, 'position' => 'CAM', 'birth_date' => '1990-08-01', 'dominant_foot' => 'left', 'nationality' => 'Brezilya', 'height' => 177, 'weight' => 70],
            ['team' => 'Başakşehir', 'first_name' => 'Boli', 'last_name' => 'Bolingoli', 'jersey_number' => 3, 'position' => 'LB', 'birth_date' => '1995-07-01', 'dominant_foot' => 'left', 'nationality' => 'Belçika', 'height' => 180, 'weight' => 75],
            ['team' => 'Başakşehir', 'first_name' => 'Junior', 'last_name' => 'Caiçara', 'jersey_number' => 2, 'position' => 'RB', 'birth_date' => '1989-04-08', 'dominant_foot' => 'right', 'nationality' => 'Brezilya', 'height' => 174, 'weight' => 70],

            // Adana Demirspor
            ['team' => 'Adana Demirspor', 'first_name' => 'Ertaç', 'last_name' => 'Özbir', 'jersey_number' => 1, 'position' => 'GK', 'birth_date' => '1989-12-02', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 186, 'weight' => 79],
            ['team' => 'Adana Demirspor', 'first_name' => 'Younes', 'last_name' => 'Belhanda', 'jersey_number' => 10, 'position' => 'CAM', 'birth_date' => '1990-02-25', 'dominant_foot' => 'right', 'nationality' => 'Fas', 'height' => 177, 'weight' => 73],
            ['team' => 'Adana Demirspor', 'first_name' => 'Britt', 'last_name' => 'Assombalonga', 'jersey_number' => 9, 'position' => 'ST', 'birth_date' => '1992-12-06', 'dominant_foot' => 'right', 'nationality' => 'Kongo', 'height' => 180, 'weight' => 76],
            ['team' => 'Adana Demirspor', 'first_name' => 'Jonas', 'last_name' => 'Svensson', 'jersey_number' => 2, 'position' => 'RB', 'birth_date' => '1993-03-06', 'dominant_foot' => 'right', 'nationality' => 'Norveç', 'height' => 180, 'weight' => 74],
            ['team' => 'Adana Demirspor', 'first_name' => 'Tarik', 'last_name' => 'Çetin', 'jersey_number' => 4, 'position' => 'CB', 'birth_date' => '1998-11-10', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 185, 'weight' => 79],

            // Antalyaspor
            ['team' => 'Antalyaspor', 'first_name' => 'Ruud', 'last_name' => 'Boffin', 'jersey_number' => 1, 'position' => 'GK', 'birth_date' => '1987-12-05', 'dominant_foot' => 'right', 'nationality' => 'Belçika', 'height' => 193, 'weight' => 87],
            ['team' => 'Antalyaspor', 'first_name' => 'Haji', 'last_name' => 'Wright', 'jersey_number' => 9, 'position' => 'ST', 'birth_date' => '1998-07-27', 'dominant_foot' => 'right', 'nationality' => 'ABD', 'height' => 191, 'weight' => 83],
            ['team' => 'Antalyaspor', 'first_name' => 'Deni', 'last_name' => 'Milošević', 'jersey_number' => 11, 'position' => 'LW', 'birth_date' => '1995-02-06', 'dominant_foot' => 'right', 'nationality' => 'Bosna Hersek', 'height' => 171, 'weight' => 63],
            ['team' => 'Antalyaspor', 'first_name' => 'Fredy', 'last_name' => 'Ribeiro', 'jersey_number' => 7, 'position' => 'RW', 'birth_date' => '1994-02-15', 'dominant_foot' => 'left', 'nationality' => 'Portekiz', 'height' => 176, 'weight' => 71],
            ['team' => 'Antalyaspor', 'first_name' => 'Bünyamin', 'last_name' => 'Balcı', 'jersey_number' => 20, 'position' => 'CM', 'birth_date' => '1994-10-08', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 180, 'weight' => 74],

            // Konyaspor
            ['team' => 'Konyaspor', 'first_name' => 'İbrahim', 'last_name' => 'Sehic', 'jersey_number' => 1, 'position' => 'GK', 'birth_date' => '1988-01-02', 'dominant_foot' => 'right', 'nationality' => 'Bosna Hersek', 'height' => 193, 'weight' => 86],
            ['team' => 'Konyaspor', 'first_name' => 'Abdülkerim', 'last_name' => 'Bardakcı', 'jersey_number' => 4, 'position' => 'CB', 'birth_date' => '1994-07-05', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 186, 'weight' => 80],
            ['team' => 'Konyaspor', 'first_name' => 'Zymer', 'last_name' => 'Bytyqi', 'jersey_number' => 10, 'position' => 'LW', 'birth_date' => '1995-07-05', 'dominant_foot' => 'right', 'nationality' => 'Kosova', 'height' => 178, 'weight' => 72],
            ['team' => 'Konyaspor', 'first_name' => 'Sokol', 'last_name' => 'Cikalleshi', 'jersey_number' => 9, 'position' => 'ST', 'birth_date' => '1990-07-27', 'dominant_foot' => 'right', 'nationality' => 'Arnavutluk', 'height' => 183, 'weight' => 79],
            ['team' => 'Konyaspor', 'first_name' => 'Ahmet', 'last_name' => 'Çalık', 'jersey_number' => 3, 'position' => 'CB', 'birth_date' => '1994-02-26', 'dominant_foot' => 'left', 'nationality' => 'Türkiye', 'height' => 184, 'weight' => 78],

            // Sivasspor
            ['team' => 'Sivasspor', 'first_name' => 'Ali', 'last_name' => 'Şaşal Vural', 'jersey_number' => 1, 'position' => 'GK', 'birth_date' => '1993-07-10', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 189, 'weight' => 80],
            ['team' => 'Sivasspor', 'first_name' => 'Erdoğan', 'last_name' => 'Yeşilyurt', 'jersey_number' => 5, 'position' => 'CDM', 'birth_date' => '1995-12-01', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 181, 'weight' => 75],
            ['team' => 'Sivasspor', 'first_name' => 'Leke', 'last_name' => 'James', 'jersey_number' => 9, 'position' => 'ST', 'birth_date' => '1992-06-23', 'dominant_foot' => 'right', 'nationality' => 'Nijerya', 'height' => 186, 'weight' => 80],
            ['team' => 'Sivasspor', 'first_name' => 'Max', 'last_name' => 'Gradel', 'jersey_number' => 10, 'position' => 'LW', 'birth_date' => '1987-11-30', 'dominant_foot' => 'right', 'nationality' => 'Fildişi Sahili', 'height' => 175, 'weight' => 72],
            ['team' => 'Sivasspor', 'first_name' => 'Caner', 'last_name' => 'Osmanpaşa', 'jersey_number' => 4, 'position' => 'CB', 'birth_date' => '1988-07-03', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 186, 'weight' => 80],

            // Kasımpaşa
            ['team' => 'Kasımpaşa', 'first_name' => 'Ertuğrul', 'last_name' => 'Taşkıran', 'jersey_number' => 1, 'position' => 'GK', 'birth_date' => '1996-06-10', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 185, 'weight' => 78],
            ['team' => 'Kasımpaşa', 'first_name' => 'Kwame', 'last_name' => 'Karikari', 'jersey_number' => 9, 'position' => 'ST', 'birth_date' => '1992-06-04', 'dominant_foot' => 'right', 'nationality' => 'Gana', 'height' => 179, 'weight' => 74],
            ['team' => 'Kasımpaşa', 'first_name' => 'Haris', 'last_name' => 'Hajradinović', 'jersey_number' => 10, 'position' => 'CAM', 'birth_date' => '1994-06-16', 'dominant_foot' => 'left', 'nationality' => 'Bosna Hersek', 'height' => 182, 'weight' => 77],
            ['team' => 'Kasımpaşa', 'first_name' => 'Tarkan', 'last_name' => 'Serbest', 'jersey_number' => 5, 'position' => 'CDM', 'birth_date' => '1995-03-22', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 182, 'weight' => 76],
            ['team' => 'Kasımpaşa', 'first_name' => 'Oğuz', 'last_name' => 'Ceylan', 'jersey_number' => 7, 'position' => 'RW', 'birth_date' => '1996-12-08', 'dominant_foot' => 'left', 'nationality' => 'Türkiye', 'height' => 176, 'weight' => 70],

            // Samsunspor
            ['team' => 'Samsunspor', 'first_name' => 'Nurullah', 'last_name' => 'Aslan', 'jersey_number' => 1, 'position' => 'GK', 'birth_date' => '1997-01-15', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 188, 'weight' => 81],
            ['team' => 'Samsunspor', 'first_name' => 'Carlo', 'last_name' => 'Costly', 'jersey_number' => 9, 'position' => 'ST', 'birth_date' => '1993-05-18', 'dominant_foot' => 'right', 'nationality' => 'Honduras', 'height' => 187, 'weight' => 82],
            ['team' => 'Samsunspor', 'first_name' => 'Landry', 'last_name' => 'Dimata', 'jersey_number' => 11, 'position' => 'ST', 'birth_date' => '1997-10-01', 'dominant_foot' => 'left', 'nationality' => 'Belçika', 'height' => 183, 'weight' => 78],
            ['team' => 'Samsunspor', 'first_name' => 'Yasin', 'last_name' => 'Öztekin', 'jersey_number' => 7, 'position' => 'LW', 'birth_date' => '1987-03-19', 'dominant_foot' => 'right', 'nationality' => 'Türkiye', 'height' => 174, 'weight' => 68],
            ['team' => 'Samsunspor', 'first_name' => 'Satka', 'last_name' => 'Gjoko', 'jersey_number' => 5, 'position' => 'CB', 'birth_date' => '1995-06-20', 'dominant_foot' => 'right', 'nationality' => 'Kuzey Makedonya', 'height' => 185, 'weight' => 80],
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
