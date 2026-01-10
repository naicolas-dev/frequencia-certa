<?php

namespace App\Gamification\Rules;

use App\Gamification\BadgeRule;
use App\Models\User;

class EarlyBirdRule implements BadgeRule
{
    public function code(): string { return 'early_bird'; }

    public function matches(User $user): bool
    {
        // Pega as últimas 5 frequências do usuário
        $ultimas = $user->frequencias()
            ->latest()
            ->take(5)
            ->get();

        if ($ultimas->count() < 5) return false;

        // Verifica se TODAS foram antes das 08:00
        foreach ($ultimas as $freq) {
            // Supondo que você tenha created_at ou um campo 'horario' time
            $hora = \Carbon\Carbon::parse($freq->horario ?? $freq->created_at)->hour;
            if ($hora >= 8) return false; 
        }

        return true;
    }
}