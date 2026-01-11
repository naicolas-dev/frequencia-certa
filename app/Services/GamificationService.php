<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use App\Gamification\BadgeEvaluator;

class GamificationService
{
    protected $evaluator;

    public function __construct(BadgeEvaluator $evaluator)
    {
        $this->evaluator = $evaluator;
    }

    public function verificarOfensiva(User $user)
    {
        $hoje = Carbon::now()->startOfDay();
        $ultimoRegistro = $user->last_streak_date ? Carbon::parse($user->last_streak_date)->startOfDay() : null;
        $precisaSalvar = false;

        // 1. LÓGICA DA OFENSIVA (Só roda se ainda não registrou hoje)
        if (!$ultimoRegistro || !$ultimoRegistro->equalTo($hoje)) {
            
            // Se foi ontem, incrementa. Se foi antes, reseta.
            if ($ultimoRegistro && $ultimoRegistro->diffInDays($hoje) == 1) {
                $user->current_streak++;
            } else {
                $user->current_streak = 1; // Começou hoje ou Recomeçou
            }

            // Atualiza recorde se necessário
            if ($user->current_streak > $user->max_streak) {
                $user->max_streak = $user->current_streak;
            }

            $user->last_streak_date = $hoje;
            $precisaSalvar = true;
        }

        if ($precisaSalvar) {
            $user->save();
        }

        // 2. AVALIAÇÃO DE BADGES (Roda SEMPRE, independente da ofensiva)
        // Isso corrige o problema: mesmo se já marcou hoje, ele verifica se tem medalha nova pendente.
        $novasConquistas = $this->evaluator->evaluate($user);

        return $novasConquistas;
    }
}