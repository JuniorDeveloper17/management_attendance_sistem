<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;


class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

      
        for ($i = 0; $i < 50; $i++) {
            Karyawan::create([
                'nip' => $faker->unique()->numerify('##########'), 
                'nama' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('Univbi12345'),
                'alamat' => $faker->address,
                'jenis_kelamin' => $faker->randomElement(['Laki-laki', 'Perempuan']),
                'tanggal_lahir' => $faker->date(),
                'foto' => null,
                'no_telp' => $faker->unique()->phoneNumber,
                'wfa' => $faker->boolean,
                'status' => 'Aktif',
                'face_id' => null,
                'id_device' => $faker->uuid,
                'id_shift' =>1, 
                'id_office' => 1, 
            ]);
        }
    }
}
//php artisan make:seeder IzinSeeder
// php artisan db:seed --class=KaryawanSeeder


