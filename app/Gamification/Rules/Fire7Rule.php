<?php

namespace App\Gamification\Rules;

use App\Gamification\BadgeRule;
use App\Models\User;

class Fire7Rule implements BadgeRule
{
    public function code(): string 
    {
        return 'fire_7';
    }

    public function matches(User $user): bool
    {
        return $user->current_streak >= 7;
    }
}