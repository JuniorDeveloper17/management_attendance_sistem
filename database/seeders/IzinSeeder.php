<?php

namespace Database\Seeders;

use App\Models\izin;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class IzinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat instance dari Faker
        $faker = Faker::create();

        for ($i = 0; $i < 50; $i++) {
            izin::create([
                'keterangan' => $faker->randomElement(['sakit', 'izin']),
                'id_karyawan'=> 106,
                'status' => $faker->boolean,  // status true atau false
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}

//php artisan db:seed --class=IzinSeeder
