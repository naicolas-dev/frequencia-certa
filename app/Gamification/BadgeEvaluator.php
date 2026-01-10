<?php

namespace App\Gamification;

use App\Models\User;
use App\Models\Badge;
use Illuminate\Support\Facades\Log;

class BadgeEvaluator
{
    protected array $rules;

    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    public function evaluate(User $user): array
    {
        $newBadges = [];
        
        // Carrega os badges que o usuário JÁ tem para não consultar o banco toda hora
        $user->load('badges');
        $ownedCodes = $user->badges->pluck('code')->toArray();

        foreach ($this->rules as $ruleClass) {
            $rule = app($ruleClass);
            $code = $rule->code();

            // Se já tem, pula
            if (in_array($code, $ownedCodes)) {
                continue;
            }

            // Se a regra bater...
            if ($rule->matches($user)) {
                $badgeModel = Badge::where('code', $code)->first();
                
                if ($badgeModel) {
                    $user->badges()->attach($badgeModel->id);
                    $newBadges[] = $badgeModel;
                    Log::info("Badge desbloqueada: {$code} para User {$user->id}");
                }
            }
        }

        return $newBadges; // Retorna o que ganhou para exibir notificação
    }
}