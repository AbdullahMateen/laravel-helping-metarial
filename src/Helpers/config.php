<?php

if (!function_exists('app_name')) {
    function app_name(): string
    {
        return config('app.name') ?? 'Website';
    }
}

if (!function_exists('app_full_name')) {
    function app_full_name(): string
    {
        return config('app.full_name') ?? 'Website';
    }
}

if (!function_exists('app_company_name')) {
    function app_company_name(): string
    {
        return config('app.company_name') ?? 'Website';
    }
}

if (!function_exists('app_url')) {
    function app_url(string|null $path = null): string
    {
        $url = rtrim(config('app.url'), '/');
        if (isset($path)) $url = $url . '/' . ltrim($path, '/');
        return $url;
    }
}

if (!function_exists('app_asset_url')) {
    function app_asset_url(string|null $path = null): string
    {
        $url = config('app.asset_url') ?? app_url();
        $url = rtrim($url, '/');
        if (isset($path)) $url = $url . '/' . ltrim($path, '/');
        return $url;
    }
}

if (!function_exists('app_domain')) {
    function app_domain(): string
    {
        return config('app.domain') ?? 'localhost';
    }
}

if (!function_exists('app_timezone')) {
    function app_timezone(): string
    {
        return config('app.timezone') ?? 'UTC';
    }
}

if (!function_exists('app_local')) {
    function app_local(): string
    {
        return config('app.locale') ?? 'en';
    }
}
