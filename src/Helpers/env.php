<?php

if (!function_exists('is_production')) {
    /**
     * @return bool
     */
    function is_production(): bool
    {
        return in_array(strtolower(config('app.env')), ['prod', 'production']);
    }
}

if (!function_exists('is_staging')) {
    /**
     * @return bool
     */
    function is_staging(): bool
    {
        return in_array(strtolower(config('app.env')), ['dev', 'development', 'stg', 'staging']);
    }
}

if (!function_exists('is_local')) {
    /**
     * @return bool
     */
    function is_local(): bool
    {
        return strtolower(config('app.env')) === 'local';
    }
}

if (!function_exists('is_testing')) {
    /**
     * @return bool
     */
    function is_testing(): bool
    {
        return in_array(strtolower(config('app.env')), ['test', 'testing']);
    }
}

if (!function_exists('is_debug_mode')) {
    /**
     * @return bool
     */
    function is_debug_mode(): bool
    {
        return config('app.debug', false);
    }
}
