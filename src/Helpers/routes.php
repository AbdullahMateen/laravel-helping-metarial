<?php

if (!function_exists('route_url_to_name')) {
    function route_url_to_name(\Illuminate\Http\Request|string $url, $method = 'get')
    {
        try {
            if ($url instanceof \Illuminate\Http\Request) {
                $method = $url->getMethod();
                $url    = \Illuminate\Support\Facades\URL::current();
            }
            $request = app('router')->getRoutes()->match(app('request')->create($url, $method));
            return $request->getName();
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('is_route_name_exists')) {
    function is_route_name_exists($routeName)
    {
        try {
            return \Illuminate\Support\Facades\Route::has($routeName);
        } catch (Exception $exception) {
            return false;
        }
    }
}

if (!function_exists('get_current_route_name')) {
    function get_current_route_name()
    {
        return \Illuminate\Support\Facades\Route::currentRouteName();
    }
}

if (!function_exists('is_current_route')) {
    function is_current_route($routeName)
    {
        return get_current_route_name() == $routeName;
    }
}

if (!function_exists('is_current_route_in')) {
    function is_current_route_in($routeNames)
    {
        $routeNames = is_array($routeNames) ? $routeNames : explode(',', $routeNames);
        return in_array(get_current_route_name(), $routeNames);
    }
}

if (!function_exists('route_url_is')) {
    function route_url_is($wildCardURL)
    {
        return Request::is($wildCardURL);
    }
}

if (!function_exists('route_is')) {
    function route_is($name)
    {
        return is_current_route($name);
    }
}

if (!function_exists('get_route_name_from_url')) {
    function get_route_name_from_url(\Illuminate\Http\Request|string $url, $method = 'get')
    {
        try {
            if ($url instanceof \Illuminate\Http\Request) {
                $method = $url->getMethod();
                $url    = \Illuminate\Support\Facades\URL::current();
            }
            $request = app('router')->getRoutes()->match(app('request')->create($url, $method));
            return $request->getName();
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('clear_intended_url')) {
    function clear_intended_url()
    {
        session()->forget('url.intended');
    }
}

if (!function_exists('logout_auth_user')) {
    function logout_auth_user($request = null)
    {
        try {
            if (!auth_check()) return redirect()->route('index');
            $redirect = (new \App\Http\Controllers\Auth\LoginController())->logout($request ?? request());
            clear_intended_url();
            return $redirect;
        } catch (Exception $exception) {
            return redirect()->route('index');
        }
    }
}

if (!function_exists('goto_route_encrypt')) {
    function goto_route_encrypt($routeName, $parameters = [])
    {
        try {
            if (!is_route_name_exists($routeName)) return null;
            return encrypt($routeName . '|:|' . json_encode($parameters));
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('goto_route_decrypt')) {
    function goto_route_decrypt($hash)
    {
        try {
            $route = explode('|:|', decrypt($hash));
            return [$routeName = $route[0], $routeParameters = json_decode($route[1], true)];
        } catch (Exception $exception) {
            return null;
        }
    }
}
