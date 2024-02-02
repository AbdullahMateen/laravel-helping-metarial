<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

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
