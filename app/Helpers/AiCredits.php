<?php

namespace App\Helpers;

/**
 * AI Credits Configuration
 * 
 * Centralizes all AI credit-related constants for the conceptual credit system.
 * 
 * IMPORTANT: 
 * - Monthly limits are REAL (user is blocked when credits reach 0)
 * - "Buy more" functionality is purely conceptual for TCC/demo
 * - Cache hits cost 0 credits
 */
class AiCredits
{
    /**
     * Maximum credits allocated per month (resets on day 1)
     */
    const MONTHLY_MAX = 100;

    /**
     * Cost for Day Check endpoint (Oracle advice for entire day)
     */
    const COST_DAY_CHECK = 10;

    /**
     * Cost for Subject Analysis endpoint (Oracle advice for single subject)
     */
    const COST_SUBJECT_ANALYSIS = 5;

    /**
     * Cost for Import Schedule endpoint (OCR or text-based schedule import)
     */
    const COST_IMPORT_SCHEDULE = 50;
}
