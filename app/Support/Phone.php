<?php

namespace App\Support;

class Phone
{
    public const DEFAULT_DDI = '55';

    public const DEFAULT_DDD = '41';

    /**
     * Remove everything that is not a number
     */
    public static function onlyDigits(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        return preg_replace('/\D+/', '', $value);
    }

    public static function isValid(?string $value): bool
    {
        if (! $value) {
            return true; // optional field
        }

        $digits = self::onlyDigits($value);

        if (! $digits) {
            return false;
        }

        $length = strlen($digits);

        if ($length < 8) {
            return false;
        }

        // International identified by: 12+ digits NOT starting with 55
        if ($length >= 12 && ! str_starts_with($digits, self::DEFAULT_DDI)) {
            return true;
        }

        // It is evaluated as a Brazilian number.
        $brNumber = $digits;

        // Strip DDI if present
        if ($length >= 12 && str_starts_with($brNumber, self::DEFAULT_DDI)) {
            $brNumber = substr($brNumber, 2);
        }

        $brLength = strlen($brNumber);

        // After stripping DDI, a valid Brazilian number must be 8, 9, 10, or 11 digits
        if (! in_array($brLength, [8, 9, 10, 11], true)) {
            return false;
        }

        // Extract the local part (without DDD)
        // If 10 or 11 digits, the first 2 are DDD, the rest is the local part
        if ($brLength === 10 || $brLength === 11) {
            $localPart = substr($brNumber, 2);
        } else {
            $localPart = $brNumber;
        }

        $localLength = strlen($localPart);

        // Validation rules for the local part
        // 9 digits MUST start with 9
        if ($localLength === 9 && ! str_starts_with($localPart, '9')) {
            return false;
        }

        // 8 digits MUST NOT start with 9
        if ($localLength === 8 && str_starts_with($localPart, '9')) {
            return false;
        }

        return true;
    }

    /**
     * Normalize for saving to the database
     * - If Brazil, tries to ensure DDI + DDD
     */
    public static function toDatabase(?string $value): ?string
    {
        $digits = self::onlyDigits($value);

        if (! $digits) {
            return null;
        }

        $length = strlen($digits);

        // Local number (8 or 9 digits)
        if ($length === 8 || $length === 9) {
            return self::DEFAULT_DDI.self::DEFAULT_DDD.$digits;
        }

        // With DDD (10 or 11)
        if ($length === 10 || $length === 11) {
            return self::DEFAULT_DDI.$digits;
        }

        // Might have DDI
        if ($length >= 12) {
            // If Brazil, keep it
            if (str_starts_with($digits, self::DEFAULT_DDI)) {
                return $digits;
            }

            // International, keep it raw
            return $digits;
        }

        return $digits;
    }

    /**
     * Format for human display
     * - Brazil: full format +55 (DD) N?NNNN-NNNN
     * - International: +DDI raw number
     */
    public static function toHuman(?string $value): ?string
    {
        $digits = self::onlyDigits($value);

        if (! $digits) {
            return null;
        }

        $length = strlen($digits);

        if ($length < 8) {
            return $digits;
        }

        // Local without DDD or DDI
        if ($length === 8 || $length === 9) {
            $ddi = self::DEFAULT_DDI;
            $ddd = self::DEFAULT_DDD;
            $number = $digits;
        }
        // With DDD
        elseif ($length === 10 || $length === 11) {
            $ddi = self::DEFAULT_DDI;
            $ddd = substr($digits, 0, 2);
            $number = substr($digits, 2);
        }
        // Might have DDI
        else {
            $ddi = substr($digits, 0, 2);
            $rest = substr($digits, 2);

            // International -> do not format, just append '+'
            if ($ddi !== self::DEFAULT_DDI) {
                return '+'.$digits;
            }

            // Full Brazil
            $ddd = substr($rest, 0, 2);
            $number = substr($rest, 2);
        }

        // Brazilian formatting
        if (strlen($number) === 8) {
            $formatted = substr($number, 0, 4).'-'.substr($number, 4);
        } elseif (strlen($number) === 9) {
            $formatted = substr($number, 0, 5).'-'.substr($number, 5);
        } else {
            if ($ddi === self::DEFAULT_DDI) {
                return '+'.$digits;
            }
            $formatted = $number;
        }

        return sprintf('+%s (%s) %s', $ddi, $ddd, $formatted);
    }
}
