<?php

if (!function_exists('display_number')) {
    function display_number($number, int $decimal = 2, string $decimalPoint = '.', string $thousandsSeparator = ''): string
    {
        return number_format($number, $decimal, $decimalPoint, $thousandsSeparator);
    }
}

if (!function_exists('human_readable_number')) {
    #[Pure]
    function human_readable_number($number, int $decimal = 2, string $decimalPoint = '.', string $thousandsSeparator = ','): string
    {
        return display_number($number, $decimal, $decimalPoint, $thousandsSeparator);
    }
}

if (!function_exists('is_zero')) {
    function is_zero($number): bool
    {
        return $number == 0;
    }
}

if (!function_exists('is_negative')) {
    function is_negative($number): bool
    {
        return $number < 0;
    }
}

if (!function_exists('is_negative_or_zero')) {
    function is_negative_or_zero($number): bool
    {
        return $number <= 0;
    }
}

if (!function_exists('is_positive')) {
    function is_positive($number): bool
    {
        return $number > 0;
    }
}

if (!function_exists('is_positive_or_zero')) {
    function is_positive_or_zero($number): bool
    {
        return $number >= 0;
    }
}

if (!function_exists('calculate_age')) {
    /**
     * @param $dateOfBirth   'Y-m-d'
     * @param $dateTill      'Y-m-d'
     * @param $todayIncluded 'true, false'
     *
     * @return int|null
     */
    function calculate_age($dateOfBirth, $dateTill = null, bool $todayIncluded = true): ?int
    {
        try {
            $dateOfBirth = Carbon::createFromFormat('Y-m-d', $dateOfBirth, app_timezone());
            $dateTill    = isset($dateTill) ? Carbon::createFromFormat('Y-m-d', $dateTill, app_timezone()) : $dateTill;
            return $todayIncluded ? Carbon::parse($dateOfBirth)->diffInYears($dateTill) : Carbon::parse($dateOfBirth)->diffInYears(Carbon::parse($dateTill)->subDay());
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('is_age_match_criteria')) {
    /**
     * @param $dateOfBirth 'Y-m-d'
     * @param $dateTill    'Y-m-d'
     * @param $operator    '==, ===, <>, !=, !==, <, <=, >, >=, <=>'
     * @param $criteria    '16'
     *
     * @return bool|int|null
     */
    function is_age_match_criteria($dateOfBirth, $dateTill = null, $operator = '<=', $criteria = 16): bool|int|null
    {
        try {
            $age = calculate_age(
                Carbon::createFromFormat('Y-m-d', $dateOfBirth, app_timezone())->format('Y-m-d'),
                isset($dateTill) ? Carbon::createFromFormat('Y-m-d', $dateTill, app_timezone())->format('Y-m-d') : $dateTill
            );
            return match ($operator) {
                '=='  => $age == $criteria,
                '===' => $age === $criteria,
                '<>'  => $age <> $criteria,
                '!='  => $age != $criteria,
                '!==' => $age !== $criteria,
                '<'   => $age < $criteria,
                '<='  => $age <= $criteria,
                '>'   => $age > $criteria,
                '>='  => $age >= $criteria,
                '<=>' => $age <=> $criteria,
            };
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('number_to_words')) {
    function number_to_words($number, $isApprox = false)
    {
        try {
            $number = str_replace(',', '', $number);

            $formatter = new NumberFormatter('en', NumberFormatter::SPELLOUT);
            $spell     = $formatter->format($number);
            $spell     = strtolower($spell);

            if ($isApprox) {
                $spells = explode(' ', $spell);
                if ($spells[1] === 'hundred') {
                    $spell = $spells[0] . ' ' . $spells[1] . ' ' . $spells[2];
                } else {
                    $spell = $spells[0] . ' ' . $spells[1];
                }
            }

            return $spell;
        } catch (Exception $exception) {
            return 'zero';
        }
    }
}

if (!function_exists('get_percentage_of_value')) {
    function get_percentage_of_value($current, $total)
    {
        if ($total == 0) return 0;
        return ($current / $total) * 100;
    }
}

if (!function_exists('get_value_of_percentage')) {
    function get_value_of_percentage($percentage, $total)
    {
        if ($percentage == 0) return 0;
        return ($percentage / 100) * $total;
    }
}

if (!function_exists('get_total_from_amount_n_percentage')) {
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
    function percentage_difference(float $amount1, float $amount2, bool $difference = true): float|int
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
    function percentage_change(float $amount, float $percentage, bool $increment = true): float|int
    {
        return $increment ? $amount * (1 + ($percentage / 100)) : $amount * (1 - ($percentage / 100));
    }
}
