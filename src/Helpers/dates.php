<?php

use Carbon\Carbon;
use Carbon\CarbonPeriod;

if (!function_exists('now_now')) {
    /**
     * @param string $timezone
     *
     * @return Carbon
     */
    function now_now(string $timezone = 'UTC'): Carbon
    {
        return now(app_timezone($timezone));
    }
}

if (!function_exists('get_date_periods_between')) {
    /**
     * @param Carbon|string      $startDate
     * @param Carbon|string|null $endDate
     * @param string             $format
     *
     * @return array
     */
    function get_date_periods_between($startDate, $endDate = null, string $format = 'd M'): array
    {
        $endDate = $endDate ?? now();

        $periods     = CarbonPeriod::create($startDate, $endDate);
        $datePeriods = [];
        foreach ($periods as $date) $datePeriods[] = $date->format($format);
        return $datePeriods;
    }
}

if (!function_exists('is_datetime_between')) {
    /**
     * @param Carbon|string|null $start
     * @param Carbon|string|null $end
     * @param Carbon|string|null $date
     * @param bool               $includeBorderDates
     * @param bool               $includeTime
     * @param string             $timezone
     *
     * @return bool
     */
    function is_datetime_between($start, $end, $date = null, bool $includeBorderDates = true, bool $includeTime = true, string $timezone = 'UTC'): bool
    {
        if (!isset($start, $end)) return false;

        $timezone = app_timezone($timezone);
        $format   = $includeTime ? 'Y-m-d H:i:s' : 'Y-m-d';

        $date      = Carbon::parse($date ?? now_now($timezone))->format($format);
        $startDate = Carbon::parse($start, $timezone)->format($format);
        $endDate   = Carbon::parse($end, $timezone)->format($format);

        return $includeBorderDates
            ? (new Carbon($date))->betweenIncluded($startDate, $endDate)
            : (new Carbon($date))->betweenExcluded($startDate, $endDate);
    }
}

if (!function_exists('is_date_between')) {
    /**
     * @param Carbon|string $date
     * @param Carbon|string $start
     * @param Carbon|string $end
     * @param string        $timezone
     *
     * @return bool
     */
    function is_date_between($date, $start, $end, string $timezone = 'UTC'): bool
    {
        return is_datetime_between($start, $end, $date, false, false, $timezone);
    }
}

if (!function_exists('is_today_between')) {
    /**
     * @param Carbon|string $start
     * @param Carbon|string $end
     * @param string        $timezone
     *
     * @return bool
     */
    function is_today_between($start, $end, string $timezone = 'UTC'): bool
    {
        return is_date_between(now_now($timezone), $start, $end, $timezone);
    }
}

if (!function_exists('display_datetime')) {
    /**
     * @param Carbon|string|null $dateTime
     * @param string             $format
     * @param string             $timezone
     * @param string             $formatType could be empty string or iso
     * @param bool               $showTodayDefault
     *
     * @return string
     */
    function display_datetime($dateTime = null, string $format = 'l jS M, Y', string $timezone = 'UTC', string $formatType = '', bool $showTodayDefault = true): string
    {
        $timezone = app_timezone($timezone);
        if (!isset($dateTime) && $showTodayDefault) {
            $date = now_now($timezone);
            return strtolower($formatType) === 'iso' ? $date->isoFormat($format) : $date->format($format);
        }
        if (is_numeric($dateTime)) {
            $date = Carbon::createFromTimestamp($dateTime, $timezone);
            return strtolower($formatType) === 'iso' ? $date->isoFormat($format) : $date->format($format);
        }
        if (isset($dateTime)) {
            $date = Carbon::parse($dateTime, $timezone);
            return strtolower($formatType) === 'iso' ? $date->isoFormat($format) : $date->format($format);
        }
        return '';
    }
}

if (!function_exists('diff_for_humans')) {
    /**
     * @param Carbon|string $date
     * @param string        $timezone
     *
     * @return string
     */
    function diff_for_humans($date, string $timezone = 'UTC'): string
    {
        $timezone = app_timezone($timezone);
        return is_numeric($date)
            ? Carbon::createFromTimestamp($date, $timezone)->diffForHumans()
            : Carbon::parse($date, $timezone)->diffForHumans();
    }
}

if (!function_exists('remaining_days_of_month')) {
    /**
     * @param Carbon|string|null $date
     * @param bool               $useGivenDateEndOfMonth
     * @param string             $timezone
     *
     * @return int
     */
    function remaining_days_of_month($date = null, bool $useGivenDateEndOfMonth = false, string $timezone = 'UTC'): int
    {
        try {
            $timezone   = app_timezone($timezone);
            $date       = Carbon::parse($date, $timezone);
            $endOfMonth = $useGivenDateEndOfMonth
                ? Carbon::parse($date, $timezone)->endOfMonth()
                : Carbon::now($timezone)->endOfMonth();

            if ($date->gt($endOfMonth)) return -1;

            return $date->diffInDays($endOfMonth);
        } catch (Exception $exception) {
            return -1;
        }
    }
}

if (!function_exists('days_between_dates')) {
    /**
     * @param Carbon|string      $end
     * @param Carbon|string|null $start
     * @param string             $timezone
     *
     * @return int
     */
    function days_between_dates($end, $start = null, string $timezone = 'UTC'): int
    {
        try {
            $timezone = app_timezone($timezone);
            $end      = Carbon::parse($end, $timezone);
            $start    = isset($start) ? Carbon::parse($start, $timezone) : Carbon::now($timezone);
            if ($start->gt($end)) return -1;
            return $start->diffInDays($end);
        } catch (Exception $exception) {
            return -1;
        }
    }
}

if (!function_exists('remaining_days_till')) {
    /**
     * @param Carbon|string      $end
     * @param Carbon|string|null $start
     * @param string             $timezone
     *
     * @return int
     */
    function remaining_days_till($end, $start = null, string $timezone = 'UTC'): int
    {
        return days_between_dates($end, $start, $timezone);
    }
}

if (!function_exists('days_in_month')) {
    /**
     * @param Carbon|string|null $date
     * @param string             $timezone
     *
     * @return int
     */
    function days_in_month($date = null, string $timezone = 'UTC'): int
    {
        try {
            return Carbon::parse($date ?? now_now(), app_timezone($timezone))->daysInMonth;
        } catch (Exception $exception) {
            return -1;
        }
    }
}

