<?php

namespace App\Gamification\Rules;

use App\Gamification\BadgeRule;
use App\Models\User;

class Fire1Rule implements BadgeRule
{
    public function code(): string
    {
        return 'fire_1';
    }

    public function matches(User $user): bool
    {
        return $user->current_streak >= 1;
    }
}