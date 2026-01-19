<?php

namespace App\Models\Concerns;

use App\Helpers\AiCredits;
use Carbon\Carbon;

/**
 * Trait HasAiCredits
 * 
 * Manages AI credit system for users with monthly resets and enforcement.
 * 
 * Usage:
 *   1. Call ensureMonthlyCreditsFresh() at the beginning of AI endpoints
 *   2. Check hasEnoughCredits($cost) before making LLM calls
 *   3. Call deductCredits($cost) ONLY on successful LLM responses
 */
trait HasAiCredits
{
    /**
     * Ensure the user's credits are fresh for the current month.
     * Resets credits to monthly max if:
     *   - credits_reset_at is null (first time)
     *   - credits_reset_at has passed (monthly reset)
     * 
     * This method is idempotent and should be called at the start of every AI request.
     *
     * @return void
     */
    public function ensureMonthlyCreditsFresh(): void
    {
        $now = Carbon::now();

        // First time setup OR monthly reset has passed
        if ($this->credits_reset_at === null || $now->gte($this->credits_reset_at)) {
            $this->ai_credits = $this->getMonthlyMaxCredits();
            
            // Set reset date to first day of NEXT month at midnight
            $this->credits_reset_at = $now->copy()->addMonth()->startOfMonth();
            
            $this->save();
        }
    }

    /**
     * Check if user has enough credits for an operation
     *
     * @param int $cost Cost of the operation
     * @return bool
     */
    public function hasEnoughCredits(int $cost): bool
    {
        return $this->ai_credits >= $cost;
    }

    /**
     * Deduct credits from user's balance (never goes below 0)
     * 
     * IMPORTANT: Only call this AFTER successful LLM response.
     * Do NOT deduct on cache hits or LLM failures.
     *
     * @param int $cost Amount to deduct
     * @return void
     */
    public function deductCredits(int $cost): void
    {
        $this->ai_credits = max(0, $this->ai_credits - $cost);
        $this->save();
    }

    /**
     * Get the monthly maximum credits for this user
     * 
     * @return int
     */
    public function getMonthlyMaxCredits(): int
    {
        return AiCredits::MONTHLY_MAX;
    }
}
