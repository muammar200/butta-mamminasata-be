<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada user di tabel users sebelum menjalankan seeder
        $user = DB::table('users')->first();

        if (!$user) {
            $this->command->warn('No users found. Please seed the users table first.');
            return;
        }

        DB::table('sliders')->insert([
            [
                'user_id' => $user->id,
                'image' => 'slider1.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'image' => 'sliderdua.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'image' => 'slidertiga.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
