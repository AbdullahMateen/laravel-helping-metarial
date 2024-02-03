<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

/*
|--------------------------------------------------------------------------
| Application/System Related Helper Functions
|--------------------------------------------------------------------------
*/

/* ==================== Env ==================== */

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




/* ==================== Config ==================== */

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




/* ==================== Routes ==================== */

if (!function_exists('get_route_name_from_url')) {
    /**
     * @param Request|string $url
     * @param string         $method
     *
     * @return string
     */
    function get_route_name_from_url($url, string $method = 'get'): string
    {
        try {
            if ($url instanceof Request) {
                $method = $url->getMethod();
                $url    = URL::current();
            }
            $request = app('router')->getRoutes()->match(app('request')->create($url, $method));
            return $request->getName();
        } catch (Exception $exception) {
            return '';
        }
    }
}

if (!function_exists('route_url_to_name')) {
    /**
     * @param Request|string $url
     * @param string         $method
     *
     * @return string
     */
    function route_url_to_name(string $url, string $method = 'get'): string
    {
        return get_route_name_from_url($url, $method);
    }
}

if (!function_exists('is_route_name_exists')) {
    /**
     * @param string $routeName
     *
     * @return false
     */
    function is_route_name_exists(string $routeName): bool
    {
        try {
            return Route::has($routeName);
        } catch (Exception $exception) {
            return false;
        }
    }
}

if (!function_exists('get_current_route_name')) {
    /**
     * @return string|null
     */
    function get_current_route_name(): ?string
    {
        return Route::currentRouteName();
    }
}

if (!function_exists('is_current_route')) {
    /**
     * @param string $routeName
     *
     * @return bool
     */
    function is_current_route(string $routeName): bool
    {
        return get_current_route_name() === $routeName;
    }
}

if (!function_exists('is_route')) {
    /**
     * @param string $name
     *
     * @return bool
     */
    function is_route(string $name): bool
    {
        return is_current_route($name);
    }
}

if (!function_exists('is_current_route_in')) {
    /**
     * @param array|string $routeNames
     *
     * @return bool
     */
    function is_current_route_in($routeNames): bool
    {
        $routeNames = is_array($routeNames) ? $routeNames : explode(',', $routeNames);
        return in_array(get_current_route_name(), $routeNames, true);
    }
}

if (!function_exists('is_route_url')) {
    /**
     * @param string $wildCardURL
     *
     * @return bool
     */
    function is_route_url(string $wildCardURL): bool
    {
        return Request::is($wildCardURL);
    }
}

if (!function_exists('clear_intended_url')) {
    /**
     * @return void
     */
    function clear_intended_url()
    {
        session()->forget('url.intended');
    }
}

if (!function_exists('logout_auth_user')) {
    /**
     * @param Request|null $request
     * @param mixed        $redirectTo
     *
     * @return RedirectResponse
     */
    function logout_auth_user(?Request $request = null, $redirectTo = 'index'): RedirectResponse
    {
        $redirect = redirect(filter_var($redirectTo, FILTER_VALIDATE_URL) ? $redirectTo : route($redirectTo));
        try {
            if (!auth_check()) {
                return $redirect;
            }
            $redirect = (new LoginController())->logout($request ?? request());
            clear_intended_url();
            return $redirect;
        } catch (Exception $exception) {
            return $redirect;
        }
    }
}

if (!function_exists('goto_route_encrypt')) {
    /**
     * @param string $routeName
     * @param array  $parameters
     *
     * @return string|null
     */
    function goto_route_encrypt(string $routeName, array $parameters = []): ?string
    {
        try {
            if (!is_route_name_exists($routeName)) {
                return null;
            }
            return encrypt($routeName . '|:|' . json_encode($parameters));
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('goto_route_decrypt')) {
    /**
     * @param string $hash
     *
     * @return string|null
     */
    function goto_route_decrypt(string $hash): ?string
    {
        try {
            $route = explode('|:|', decrypt($hash));
            return route($route[0], json_decode($route[1], true)); //  [$routeName = $route[0], $routeParameters = json_decode($route[1], true)];
        } catch (Exception $exception) {
            return null;
        }
    }
}




/* ==================== General ==================== */

if (!function_exists('app_logo')) {
    /**
     * @param string $logo
     * @param string $theme
     *
     * @return string
     */
    function app_logo(string $logo = 'icon', string $theme = 'light'): string
    {
        return match ($logo) {
            'icon'  => asset("assets/images/$theme/logo.png"),
            'sm'    => asset("assets/images/$theme/logo.png"),
            'lg'    => asset("assets/images/$theme/logo.png"),
            'full'  => asset("assets/images/$theme/logo.png"),
            'text'  => asset("assets/images/$theme/logo.png"),
            default => asset("assets/images/$theme/logo.png"),
        };
    }
}

if (!function_exists('app_copyright')) {
    /**
     * @param string $name
     *
     * @return string
     */
    function app_copyright(string $name = 'Website'): string
    {
        return sprintf('Copyright © %s %s. All rights reserved', now_now()->format('Y'), app_name($name));
    }
}

if (!function_exists('app_copyright_long')) {
    /**
     * @param string $name
     *
     * @return string
     */
    function app_copyright_long(string $name = 'Website'): string
    {
        return app_copyright($name);
    }
}

if (!function_exists('webpage_title')) {
    /**
     * @param string $title
     * @param bool   $postfix
     * @param string $name
     *
     * @return string
     */
    function webpage_title(string $title, bool $postfix = true, string $name = 'Website'): string
    {
        return $postfix ? sprintf('%s | %s', $title, app_name($name)) : $title;
    }
}

if (!function_exists('email_subject')) {
    /**
     * @param string $subject
     * @param bool   $showAppName
     *
     * @return string
     */
    function email_subject(string $subject, bool $showAppName = true): string
    {
        return $showAppName ? sprintf('%s - %s', $subject, app_full_name()) : $subject;
    }
}

if (!function_exists('is_api')) {
    /**
     * @param Request|null $request
     * @param string       $header
     *
     * @return bool
     */
    function is_api(?Request $request = null, string $header = ''): bool
    {
        try {
            $req    = $request ?? request();
            $header = $header ?? '';
            return isset($req) && $req->hasHeader($header);
        } catch (Exception $exception) {
            return false;
        }
    }
}

if (!function_exists('get_morphs_maps')) {
    /**
     * @param Model|string|null $class
     *
     * @return false|int|string|string[]
     */
    function get_morphs_maps($class = null)
    {
        $maps = [
            'app' => 'app',
            // 'user' => User::class,
        ];

        if (isset($class)) {
            $class = $class instanceof Model && PHP_VERSION[0] <= 7 ? get_class($class) : $class::class;
            return array_search($class, $maps);
        }

        return $maps;
    }
}

if (!function_exists('get_model_table')) {
    /**
     * @param Model|string $model
     *
     * @return string|null
     */
    function get_model_table($model): ?string
    {
        try {
            if (is_string($model)) $model = (new $model);
            return $model->getTable();
        } catch (Exception $exception) {
            return null;
        }
    }
}
