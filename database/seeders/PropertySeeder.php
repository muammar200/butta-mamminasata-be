<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertySeeder extends Seeder
{
    public function run()
    {
        // Tipe properti per kategori dengan lebih banyak variasi
        $propertyTypes = [
            1 => [
                ['type' => 'Tipe 36/90', 'price' => 350000000, 'size' => '36 m2', 'area' => '90 m2'],
                ['type' => 'Tipe 45/120', 'price' => 500000000, 'size' => '45 m2', 'area' => '120 m2'],
                ['type' => 'Tipe 54/150', 'price' => 650000000, 'size' => '54 m2', 'area' => '150 m2'],
                ['type' => 'Tipe 60/180', 'price' => 800000000, 'size' => '60 m2', 'area' => '180 m2'],
                ['type' => 'Tipe 70/200', 'price' => 950000000, 'size' => '70 m2', 'area' => '200 m2'],
            ],
            2 => [
                ['type' => 'Tipe 80/200', 'price' => 1100000000, 'size' => '80 m2', 'area' => '200 m2'],
                ['type' => 'Tipe 90/220', 'price' => 1250000000, 'size' => '90 m2', 'area' => '220 m2'],
                ['type' => 'Tipe 100/250', 'price' => 1400000000, 'size' => '100 m2', 'area' => '250 m2'],
                ['type' => 'Tipe 120/300', 'price' => 1600000000, 'size' => '120 m2', 'area' => '300 m2'],
                ['type' => 'Tipe 150/350', 'price' => 1900000000, 'size' => '150 m2', 'area' => '350 m2'],
            ],
            3 => [
                ['type' => 'Kavling Premium', 'price' => 120000000, 'size' => '60 m2', 'area' => '120 m2'],
                ['type' => 'Kavling Reguler', 'price' => 180000000, 'size' => '80 m2', 'area' => '160 m2'],
                ['type' => 'Kavling Ekonomis', 'price' => 250000000, 'size' => '100 m2', 'area' => '200 m2'],
                ['type' => 'Kavling Sudut', 'price' => 300000000, 'size' => '120 m2', 'area' => '240 m2'],
                ['type' => 'Kavling Strategis', 'price' => 350000000, 'size' => '140 m2', 'area' => '280 m2'],
            ],
            4 => [
                ['type' => 'Kavling Cluster', 'price' => 140000000, 'size' => '70 m2', 'area' => '140 m2'],
                ['type' => 'Kavling Perumahan', 'price' => 200000000, 'size' => '90 m2', 'area' => '180 m2'],
                ['type' => 'Kavling Investasi', 'price' => 280000000, 'size' => '120 m2', 'area' => '250 m2'],
                ['type' => 'Kavling Premium+', 'price' => 350000000, 'size' => '150 m2', 'area' => '300 m2'],
                ['type' => 'Kavling Hijau', 'price' => 400000000, 'size' => '180 m2', 'area' => '350 m2'],
            ],
            5 => [
                ['type' => 'Kavling Industri', 'price' => 200000000, 'size' => '100 m2', 'area' => '200 m2'],
                ['type' => 'Kavling Komersial', 'price' => 300000000, 'size' => '150 m2', 'area' => '300 m2'],
                ['type' => 'Kavling Ruko', 'price' => 400000000, 'size' => '200 m2', 'area' => '400 m2'],
                ['type' => 'Kavling Toko', 'price' => 500000000, 'size' => '250 m2', 'area' => '500 m2'],
                ['type' => 'Kavling Bisnis', 'price' => 600000000, 'size' => '300 m2', 'area' => '600 m2'],
            ],
        ];

        // Deskripsi otomatis
        $houseDescriptions = [
            'Hunian modern dengan desain minimalis yang nyaman dan strategis.',
            'Rumah elegan dengan fasilitas lengkap, cocok untuk keluarga.',
            'Lingkungan asri dan tenang, ideal untuk tempat tinggal masa depan.',
            'Rumah eksklusif dengan konsep eco-living dan ramah lingkungan.',
            'Hunian bergaya mewah dengan akses mudah ke pusat kota.',
        ];

        $landDescriptions = [
            'Kavling strategis dengan akses mudah ke fasilitas umum.',
            'Lahan luas cocok untuk investasi jangka panjang.',
            'Lokasi premium dengan harga kompetitif dan prospek cerah.',
            'Tanah kavling siap bangun dengan infrastruktur lengkap.',
            'Lahan investasi dengan potensi pertumbuhan nilai tinggi.',
        ];

        foreach ($propertyTypes as $category_id => $types) {
            shuffle($types); // Acak tipe agar tidak sama dalam kategori

            foreach ($types as $property) {
                $description = in_array($category_id, [1, 2])
                    ? $houseDescriptions[array_rand($houseDescriptions)]
                    : $landDescriptions[array_rand($landDescriptions)];

                $property_id = DB::table('properties')->insertGetId([
                    'user_id' => 1,
                    'category_id' => $category_id,
                    'type' => $property['type'],
                    'price' => $property['price'],
                    'size' => $property['size'],
                    'area' => $property['area'],
                    'phone' => '082336815285',
                    'description' => $description,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Insert gambar sesuai kategori
                $images = in_array($category_id, [1, 2])
                    ? ['gambar1.png', 'gambar2.png', 'gambar3.png']
                    : ['kavling-satu.jpg', 'kavling-dua.jpg', 'kavling-tiga.jpg'];

                foreach ($images as $image) {
                    DB::table('property_images')->insert([
                        'property_id' => $property_id,
                        'image' => $image,
                    ]);
                }

                // Hanya insert fasilitas & ruangan untuk rumah (category_id 1 & 2)
                if (in_array($category_id, [1, 2])) {
                    $facilities = ['Kolam Renang', 'Garasi', 'Taman', 'CCTV', 'Gym'];
                    $selectedFacilities = array_rand(array_flip($facilities), rand(2, 4));
                    foreach ((array) $selectedFacilities as $facility) {
                        DB::table('property_facilities')->insert([
                            'property_id' => $property_id,
                            'facility_name' => $facility,
                        ]);
                    }

                    // Insert rooms
                    $rooms = [
                        ['room_type' => 'Kamar Tidur', 'quantity' => rand(2, 5)],
                        ['room_type' => 'Kamar Mandi', 'quantity' => rand(1, 3)],
                        ['room_type' => 'Ruang Tamu', 'quantity' => 1],
                        ['room_type' => 'Dapur', 'quantity' => 1],
                    ];

                    foreach ($rooms as $room) {
                        DB::table('property_rooms')->insert([
                            'property_id' => $property_id,
                            'room_type' => $room['room_type'],
                            'quantity' => $room['quantity'],
                        ]);
                    }
                }
            }
        }
    }
}
