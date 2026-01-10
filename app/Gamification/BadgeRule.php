<?php

namespace App\Gamification;

use App\Models\User;

interface BadgeRule
{
    /**
     * O código do badge no banco de dados (ex: 'fire_7')
     */
    public function code(): string;

    /**
     * Verifica se o usuário cumpre os requisitos
     */
    public function matches(User $user): bool;
}