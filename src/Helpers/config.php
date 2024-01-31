<?php

if (!function_exists('app_name')) {
    /**
     * @param string $default
     *
     * @return string
     */
    function app_name(string $default = 'Website'): string
    {
        return config('app.name', $default);
    }
}

if (!function_exists('app_full_name')) {
    /**
     * @param string $default
     *
     * @return string
     */
    function app_full_name(string $default = 'Website'): string
    {
        return config('app.full_name', $default);
    }
}

if (!function_exists('app_company_name')) {
    /**
     * @param string $default
     *
     * @return string
     */
    function app_company_name(string $default = 'Website'): string
    {
        return config('app.company_name', $default);
    }
}

if (!function_exists('app_url')) {
    /**
     * @param string|null $path
     *
     * @return string
     */
    function app_url(?string $path = null): string
    {
        $url = rtrim(config('app.url'), '/');
        return isset($path) ? sprintf("$url/%s", ltrim($path, '/')) : $url;
    }
}

if (!function_exists('app_asset_url')) {
    /**
     * @param string|null $path
     *
     * @return string
     */
    function app_asset_url(?string $path = null): string
    {
        $url = rtrim(config('app.asset_url') ?? app_url(), '/');
        return isset($path) ? sprintf("$url/%s", ltrim($path, '/')) : $url;
    }
}

if (!function_exists('app_domain')) {
    /**
     * @param string $default
     *
     * @return string
     */
    function app_domain(string $default = '127.0.0.1:8000'): string
    {
        return config('app.domain', $default);
    }
}

if (!function_exists('app_timezone')) {
    /**
     * @param string $default
     *
     * @return string
     */
    function app_timezone(string $default = 'UTC'): string
    {
        return config('app.timezone', $default);
    }
}

if (!function_exists('app_locale')) {
    /**
     * @param string $default
     *
     * @return string
     */
    function app_locale(string $default = 'en'): string
    {
        return config('app.locale', $default);
    }
}
