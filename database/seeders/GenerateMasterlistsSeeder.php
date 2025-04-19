<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Masterlist;
use Faker\Factory as Faker;

class GenerateMasterlistsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker       = Faker::create('en_PH');
        $barangangays = [
            'Bugwak', 'Kapalaran', 'Osmeña', 'Poblacion', 'Lourdes',
            'Mac Arthur', 'Miaray', 'Migcuya', 'Dolorosa',
            'Barongkot', 'Kianggat', 'New Visayas'
        ];
        $extensions  = ['', 'Jr.', 'Sr.', 'III'];
        $seen        = [];
        $count       = 0;

        while ($count < 1000) {
            $first    = $faker->firstName();
            $middle   = $faker->optional(0.5, '')->firstName();
            $last     = $faker->lastName();
            $ext      = $faker->randomElement($extensions);
            $brgy     = $faker->randomElement($barangangays);

            // build a key to avoid exact duplicates
            $key = strtolower("{$first}|{$middle}|{$last}|{$ext}|{$brgy}");

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;

            Masterlist::create([
                'firstName'     => $first,
                'middleName'    => $middle,
                'familyName'    => $last,
                'nameExtension' => $ext,
                'barangay'      => $brgy,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            $count++;
        }
    }
}
