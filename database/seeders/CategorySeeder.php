<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'name' => 'Perumahan Grand Pangeran City, Hertasning',
                'slug' => Str::slug('Perumahan Grand Pangeran City, Hertasning', '-'),
                'address' => 'Jl. Pao-Pao Permai Hertasning',
                'office_hours' => '10.00 - 17.00',
                'type' => 'rumah',
                'image' => 'perumahan-icon.png',
                'latitude' => -5.1887136,
                'longitude' => 119.4648811,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Perumahan The Royal Pangeran, Paccinongang',
                'slug' => Str::slug('Perumahan The Royal Pangeran, Paccinongang', '-'),
                'address' => 'Jl. Yusuf Bauty',
                'office_hours' => '10.00 - 17.00',
                'type' => 'rumah',
                'image' => 'perumahan-icon-1.png',
                'latitude' => -5.2063359,
                'longitude' => 119.4625641,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kavling The Campus View Samata, Malaklang',
                'slug' => Str::slug('Kavling The Campus View Samata, Malaklang', '-'),
                'address' => 'Samata',
                'office_hours' => '10.00 - 17.00',
                'type' => 'kavling',
                'image' => 'kavling-icon.png',
                'latitude' => -5.2088316,
                'longitude' => 119.5115404,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kavling Firdaus Hills/Villa Firdaus, Bilayyah Pattallasang',
                'slug' => Str::slug('Kavling Firdaus Hills/Villa Firdaus, Bilayyah Pattallasang', '-'),
                'address' => 'Jl. Dusun Bilayya',
                'office_hours' => '10.00 - 17.00',
                'type' => 'kavling',
                'image' => 'kavling-icon-2.png',
                'latitude' => -5.2303949,
                'longitude' => 119.5977383,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kavling Citra Firdaus, Bilayyah Pattallasang',
                'slug' => Str::slug('Kavling Citra Firdaus, Bilayyah Pattallasang', '-'),
                'address' => 'Jl. Bontotangnga',
                'office_hours' => '10.00 - 17.00',
                'type' => 'kavling',
                'image' => 'kavling-icon-3.png',
                'latitude' => -5.2303950,
                'longitude' => 119.5977384,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


    }
}
