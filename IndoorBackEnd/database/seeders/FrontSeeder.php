<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\forntEnd;

class FrontSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        forntEnd::create([
            'home' => 'public/forntEnd/663bc3a150924.png',
            'navigation' => 'public/forntEnd/663bc3a152578.png',
            'more' => 'public/forntEnd/663bc3a153367.png',
        ]);
    }
}