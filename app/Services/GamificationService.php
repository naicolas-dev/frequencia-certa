<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class GamificationService
{
    public function verificarOfensiva(User $user)
    {
        $hoje = Carbon::now()->startOfDay();
        $ultimoRegistro = $user->last_streak_date ? Carbon::parse($user->last_streak_date)->startOfDay() : null;

        // Se já registrou hoje, não faz nada
        if ($ultimoRegistro && $ultimoRegistro->equalTo($hoje)) {
            return;
        }

        // Se foi ontem, incrementa. Se foi antes, reseta.
        if ($ultimoRegistro && $ultimoRegistro->diffInDays($hoje) == 1) {
            $user->current_streak++;
        } else {
            $user->current_streak = 1; // Começou hoje
        }

        // Atualiza recorde se necessário
        if ($user->current_streak > $user->max_streak) {
            $user->max_streak = $user->current_streak;
        }

        $user->last_streak_date = $hoje;
        $user->save();
        
        $this->verificarBadges($user);
    }

    private function verificarBadges(User $user)
    {
        $badges = $user->badges ?? [];
        $novasBadges = [];

        // Badge 1: Chama Acesa (3 dias seguidos)
        if ($user->current_streak >= 3 && !in_array('fire_3', $badges)) {
            $novasBadges[] = 'fire_3';
        }

        // Badge 2: Guerreiro (7 dias seguidos)
        if ($user->current_streak >= 7 && !in_array('warrior_7', $badges)) {
            $novasBadges[] = 'warrior_7';
        }

        // Se ganhou algo novo, salva
        if (!empty($novasBadges)) {
            $user->badges = array_merge($badges, $novasBadges);
            $user->save();
            // Aqui você poderia disparar uma notificação visual no front
        }
    }
}