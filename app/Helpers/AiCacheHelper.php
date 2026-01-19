<?php

namespace App\Helpers;

/**
 * Centralized AI Cache Key Management
 * 
 * IMPORTANT: All AI-related cache operations MUST use this helper
 * to ensure consistency across controllers and prevent cache key drift.
 * 
 * Usage:
 *   $key = AiCacheHelper::dayCheckKey($userId, $date);
 *   Cache::put($key, $data, $ttl);
 *   Cache::forget($key);
 */
class AiCacheHelper
{
    /**
     * Current cache version for day-check endpoint.
     * Bump this when you change the prompt or response structure.
     */
    const DAY_CHECK_VERSION = 'v1';

    /**
     * Generate cache key for daily AI advice.
     * 
     * @param int $userId
     * @param string $date Format: Y-m-d
     * @return string
     */
    public static function dayCheckKey(int $userId, string $date): string
    {
        return "ai:day_check:{$userId}:{$date}:" . self::DAY_CHECK_VERSION;
    }

    /**
     * Invalidate (forget) the day-check cache for a specific user and date.
     * 
     * Call this whenever data changes that affects day advice:
     * - Attendance updates (FrequenciaController)
     * - Schedule changes (GradeHorariaController)
     * - Event changes (EventoController)
     * - Discipline date changes (DisciplinaController)
     * 
     * @param int $userId
     * @param string $date Format: Y-m-d
     * @return bool
     */
    public static function bustDayCheck(int $userId, string $date): bool
    {
        $key = self::dayCheckKey($userId, $date);
        return \Cache::forget($key);
    }
}
