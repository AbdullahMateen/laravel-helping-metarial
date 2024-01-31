<?php

if (!function_exists('is_production')) {
    function is_production(): bool
    {
        return in_array(strtolower(config('app.env')), ['prod', 'production']);
    }
}

if (!function_exists('is_staging')) {
    function is_staging(): bool
    {
        return in_array(strtolower(config('app.env')), ['dev', 'development', 'stg', 'staging']);
    }
}

if (!function_exists('is_local')) {
    function is_local(): bool
    {
        return strtolower(config('app.env')) === 'local';
    }
}

if (!function_exists('is_testing')) {
    function is_testing(): bool
    {
        return in_array(strtolower(config('app.env')), ['test', 'testing']);
    }
}

if (!function_exists('is_debug_mode')) {
    function is_debug_mode(): mixed
    {
        return config('app.debug');
    }
}
