<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AvatarBorderSeeder extends Seeder
{
    public function run()
    {
        $borders = [
            ['name' => 'Common Green', 'rarity' => 'common', 'price' => 30, 'color' => '#22c55e'],
            ['name' => 'Common Blue', 'rarity' => 'common', 'price' => 30, 'color' => '#3b82f6'],
            ['name' => 'Rare Purple', 'rarity' => 'rare', 'price' => 80, 'color' => '#8b5cf6'],
            ['name' => 'Rare Cyan', 'rarity' => 'rare', 'price' => 80, 'color' => '#06b6d4'],
            ['name' => 'Epic Orange', 'rarity' => 'epic', 'price' => 200, 'color' => '#f97316'],
            ['name' => 'Epic Pink', 'rarity' => 'epic', 'price' => 200, 'color' => '#ec4899'],
            ['name' => 'Legendary Gold', 'rarity' => 'legendary', 'price' => 500, 'color' => '#f59e0b'],
            ['name' => 'Legendary Rainbow', 'rarity' => 'legendary', 'price' => 500, 'color' => 'linear-gradient(45deg, #ff0000, #ff7f00, #ffff00, #00ff00, #0000ff, #4b0082, #8b00ff)'],
        ];

        DB::table('avatar_borders')->insert($borders);
    }
}
