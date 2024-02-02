<?php

use Carbon\Carbon;
use Carbon\CarbonPeriod;

if (!function_exists('now_now')) {
    /**
     * @param string|null $timezone
     *
     * @return Carbon
     */
    function now_now(?string $timezone = null): Carbon
    {
        return now($timezone ?? app_timezone());
    }
}

if (!function_exists('get_date_periods_between')) {
    /**
     * @param $startDate
     * @param $endDate
     * @param $format
     *
     * @return CarbonPeriod
     */
    function get_date_periods_between($startDate, $endDate = null, $format = 'd M'): CarbonPeriod
    {
        $endDate = $endDate ?? now();

        $periods = CarbonPeriod::create($startDate, $endDate);
        foreach ($periods as $index => $date) {
            $periods[$index] = $date->format($format);
        }
        return $periods;
    }
}

if (!function_exists('is_today_between')) {
    function is_today_between($start, $end, $timezone = null)
    {
        try {
            if (!isset($start, $end)) return false;
            return Carbon::now($timezone ?? app_timezone())->between(
                Carbon::parse($start, $timezone ?? app_timezone()),
                Carbon::parse($end, $timezone ?? app_timezone())->addDay()->subSecond()
            );
        } catch (Exception $exception) {
            return false;
        }
    }
}

if (!function_exists('display_datetime')) {
    function display_datetime($dateTime = null, $format = 'l jS M, Y', $timezone = null, $formatType = '', $showTodayDefault = true)
    {
        $timezone = $timezone ?? app_timezone();
        if (!isset($dateTime) && $showTodayDefault) {
            $date = now_now();
            if (strtolower($formatType) === 'iso') return $date->isoFormat($format);
            return $date->format($format);
        }
        if (is_numeric($dateTime)) {
            $date = \Carbon\Carbon::createFromTimestamp($dateTime)->timezone($timezone);
            if (strtolower($formatType) === 'iso') return $date->isoFormat($format);
            return $date->format($format);
        }
        if (isset($dateTime)) {
            $date = \Carbon\Carbon::parse($dateTime, $timezone)->timezone($timezone);
            if (strtolower($formatType) === 'iso') return $date->isoFormat($format);
            return $date->format($format);
        }
        return '';
    }
}

if (!function_exists('diff_for_humans')) {
    function diff_for_humans($date, $timezone = null)
    {
        $timezone = $timezone ?? app_timezone();

        return is_numeric($date)
            ? Carbon::createFromTimestamp($date)->timezone($timezone)->diffForHumans()
            : Carbon::parse($date)->timezone($timezone)->diffForHumans();
    }
}

if (!function_exists('remaining_days_of_month')) {
    function remaining_days_of_month($date = null, $useGivenDateEndOfMonth = false)
    {
        try {
            $date       = Carbon::parse($date, app_timezone());
            $endOfMonth = $useGivenDateEndOfMonth
                ? Carbon::parse($date, app_timezone())->endOfMonth()
                : Carbon::now(app_timezone())->endOfMonth();

            if ($date->gt($endOfMonth)) return -1;

            return $date->diffInDays($endOfMonth);
        } catch (Exception $exception) {
            return -1;
        }
    }
}

if (!function_exists('days_between_dates')) {
    function days_between_dates($dateTo, $dateFrom = null)
    {
        try {
            $dateTo   = Carbon::parse($dateTo, app_timezone());
            $dateFrom = isset($dateFrom)
                ? Carbon::parse($dateFrom, app_timezone())
                : Carbon::now(app_timezone());

            if ($dateFrom->gt($dateTo)) return -1;

            return $dateFrom->diffInDays($dateTo);
        } catch (Exception $exception) {
            return -1;
        }
    }
}

if (!function_exists('remaining_days_till')) {
    function remaining_days_till($dateTo, $dateFrom = null)
    {
        try {
            $dateTo   = Carbon::parse($dateTo, app_timezone());
            $dateFrom = isset($dateFrom)
                ? Carbon::parse($dateFrom, app_timezone())
                : Carbon::now(app_timezone());

            if ($dateFrom->gt($dateTo)) return -1;

            return $dateFrom->diffInDays($dateTo);
        } catch (Exception $exception) {
            return -1;
        }
    }
}

if (!function_exists('is_datetime_between')) {
    function is_datetime_between($start, $end, $date = null, $includeBorderDates = true)
    {
        $date = \Carbon\Carbon::parse($date)->format('Y-m-d H:i:s') ?? now()->format('Y-m-d H:i');

        $startDate = \Carbon\Carbon::parse($start)->format('Y-m-d H:i:s');
        $endDate   = \Carbon\Carbon::parse($end)->format('Y-m-d H:i:s');
        $check     = $includeBorderDates
            ? (new \Carbon\Carbon($date))->betweenIncluded($startDate, $endDate)
            : (new \Carbon\Carbon($date))->betweenExcluded($startDate, $endDate);

        return $check;
    }
}

if (!function_exists('days_in_month')) {
    function days_in_month($date = null)
    {
        try {
            return isset($date)
                ? Carbon::parse($date, app_timezone())->daysInMonth
                : Carbon::now(app_timezone())->daysInMonth;
        } catch (Exception $exception) {
            return -1;
        }
    }
}

