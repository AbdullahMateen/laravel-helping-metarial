<?php


use Carbon\Carbon;

if (!function_exists('display_number')) {
    /**
     * @param string|int|float $number
     * @param int              $decimal
     * @param string           $decimalPoint
     * @param string           $thousandsSeparator
     *
     * @return string
     */
    function display_number($number, int $decimal = 2, string $decimalPoint = '.', string $thousandsSeparator = ','): string
    {
        return number_format($number, $decimal, $decimalPoint, $thousandsSeparator);
    }
}

if (!function_exists('to_number')) {
    /**
     * @param string|int|float $number
     * @param int              $decimal
     * @param string           $decimalPoint
     * @param string           $thousandsSeparator
     *
     * @return string
     */
    function to_number($number, int $decimal = 2, string $decimalPoint = '.', string $thousandsSeparator = ''): string
    {
        return display_number($number, $decimal, $decimalPoint, $thousandsSeparator);
    }
}

if (!function_exists('human_readable_number')) {
    /**
     * @param string|int|float $number
     * @param int              $decimal
     * @param string           $decimalPoint
     * @param string           $thousandsSeparator
     *
     * @return string
     */
    function human_readable_number($number, int $decimal = 2, string $decimalPoint = '.', string $thousandsSeparator = ','): string
    {
        return display_number($number, $decimal, $decimalPoint, $thousandsSeparator);
    }
}

if (!function_exists('is_zero')) {
    /**
     * @param mixed $number
     *
     * @return bool
     */
    function is_zero($number): bool
    {
        return is_numeric($number) && (int) $number === 0;
    }
}

if (!function_exists('is_negative')) {
    /**
     * @param mixed $number
     *
     * @return bool
     */
    function is_negative($number): bool
    {
        return is_numeric($number) && $number < 0;
    }
}

if (!function_exists('is_negative_or_zero')) {
    /**
     * @param mixed $number
     *
     * @return bool
     */
    function is_negative_or_zero($number): bool
    {
        return is_numeric($number) && $number <= 0;
    }
}

if (!function_exists('is_positive')) {
    /**
     * @param mixed $number
     *
     * @return bool
     */
    function is_positive($number): bool
    {
        return is_numeric($number) && $number > 0;
    }
}

if (!function_exists('is_positive_or_zero')) {
    /**
     * @param mixed $number
     *
     * @return bool
     */
    function is_positive_or_zero($number): bool
    {
        return is_numeric($number) && $number >= 0;
    }
}

if (!function_exists('calculate_age')) {
    /**
     * @param Carbon|string      $dateOfBirth
     * @param Carbon|string|null $dateTill
     * @param bool               $todayIncluded
     *
     * @return int|null
     */
    function calculate_age($dateOfBirth, $dateTill = null, bool $todayIncluded = true): ?int
    {
        try {
            $dateTill    = Carbon::parse($dateTill, app_timezone());
            $dateOfBirth = Carbon::parse($dateOfBirth, app_timezone());
            return $todayIncluded
                ? Carbon::parse($dateOfBirth)->diffInYears($dateTill)
                : Carbon::parse($dateOfBirth)->diffInYears($dateTill->subDay());
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('is_age_acceptable')) {
    /**
     * @param Carbon|string      $dateOfBirth 'Y-m-d'
     * @param Carbon|string|null $dateTill    'Y-m-d'
     * @param string             $operator    "<", "lt", "<=", "le", ">", "gt", ">=", "ge", "==", "=", "eq", "!=", "<>", "ne"
     * @param int                $criteria    '16'
     *
     * @return bool|null
     */
    function is_age_acceptable($dateOfBirth, $dateTill = null, string $operator = '<=', int $criteria = 16): ?bool
    {
        try {
            $age = calculate_age(
                Carbon::parse($dateOfBirth, app_timezone())->format('Y-m-d'),
                Carbon::parse($dateTill, app_timezone())->format('Y-m-d')
            );
            return version_compare($age, $criteria, $operator);
            //            return match ($operator) {
            //                '=='  => $age == $criteria,
            //                '===' => $age === $criteria,
            //                '<>'  => $age <> $criteria,
            //                '!='  => $age != $criteria,
            //                '!==' => $age !== $criteria,
            //                '<'   => $age < $criteria,
            //                '<='  => $age <= $criteria,
            //                '>'   => $age > $criteria,
            //                '>='  => $age >= $criteria,
            //                '<=>' => $age <=> $criteria,
            //            };
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('number_to_words')) {
    /**
     * @param int|float|string $number
     *
     * @return string
     */
    function number_to_words($number): string
    {
        try {
            $number = str_replace(',', '', $number);

            $formatter = new NumberFormatter('en', NumberFormatter::SPELLOUT);
            $spell     = $formatter->format($number);
            $spell     = strtolower($spell);

            return $spell;
        } catch (Exception $exception) {
            return '';
        }
    }
}

if (!function_exists('get_percentage_of_value')) {
    /**
     * @param $current
     * @param $total
     *
     * @return float|int
     */
    function get_percentage_of_value($current, $total)
    {
        if ($total == 0) return 0;
        return ($current / $total) * 100;
    }
}

if (!function_exists('get_value_of_percentage')) {
    /**
     * @param $percentage
     * @param $total
     *
     * @return float|int
     */
    function get_value_of_percentage($percentage, $total)
    {
        if ($percentage == 0) return 0;
        return ($percentage / 100) * $total;
    }
}

if (!function_exists('get_total_from_amount_n_percentage')) {
    /**
     * @param $amount
     * @param $percentage
     *
     * @return float|int
     */
    function get_total_from_amount_n_percentage($amount, $percentage)
    {
        if ($amount == 0) return 0;
        if ($percentage == 0) return 0;
        return ($amount / $percentage) * 100;
    }
}

if (!function_exists('percentage_difference')) {
    /**
     * if true it will calculate the difference b/w amount1 and amount2 in percentage if false it will tell amount2 is ? % increment/decrement of amount2
     *
     * @param float $amount1    Numeric value 1
     * @param float $amount2    Numeric value 1
     * @param bool  $difference if true it will calculate the difference b/w amount1 and amount2 in percentage if false it will tell amount2 is ? % increment/decrement of amount2
     *
     * @return float|int
     */
    function percentage_difference(float $amount1, float $amount2, bool $difference = true)
    {
        if ($difference) {
            return (abs($amount1 - $amount2) / (($amount1 + $amount2) / 2)) * 100;
        }

        return (abs($amount1 - $amount2) / $amount1) * 100;
    }
}

if (!function_exists('percentage_change')) {
    /**
     * This function will increment/decrement the given amount by given percentage
     *
     * @param float $amount     Numeric value
     * @param float $percentage Percentage value
     * @param bool  $increment  if true it will increment if false it will decrement
     *
     * @return float|int
     */
    function percentage_change(float $amount, float $percentage, bool $increment = true)
    {
        return $increment ? $amount * (1 + ($percentage / 100)) : $amount * (1 - ($percentage / 100));
    }
}
