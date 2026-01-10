<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Badge;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            // 🔥 Constância
            ['code' => 'fire_1', 'name' => 'Primeira chama', 'icon' => '🕯️', 'category' => 'streak', 'description' => '1 dia de ofensiva'],
            ['code' => 'fire_3', 'name' => 'Em aquecimento', 'icon' => '🔥', 'category' => 'streak', 'description' => '3 dias seguidos'],
            ['code' => 'fire_7', 'name' => 'Uma semana invicto', 'icon' => '🔥🔥', 'category' => 'streak', 'description' => '7 dias seguidos'],
            ['code' => 'fire_30', 'name' => 'Vulcão', 'icon' => '🌋', 'category' => 'streak', 'description' => '30 dias consecutivos'],
            
            // 🧠 Comportamento
            ['code' => 'early_bird', 'name' => 'Acorda cedo', 'icon' => '🌅', 'category' => 'behavior', 'description' => 'Marcou antes das 08h por 5 dias'],
            
            // 🛡️ Resiliência
            ['code' => 'phoenix', 'name' => 'Fênix', 'icon' => '🐦‍🔥', 'category' => 'resilience', 'description' => 'Recuperou uma grande ofensiva'],
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(['code' => $badge['code']], $badge);
        }
    }
}
