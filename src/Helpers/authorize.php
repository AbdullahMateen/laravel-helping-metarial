<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

if (!function_exists('policy_authorization')) {
    /**
     * @param Model      $user
     * @param string     $ability
     * @param Model|null $model
     *
     * @return bool
     */
    function policy_authorization(Model $user, string $ability, ?Model $model = null): bool
    {
        if ($user->role->isReserved()) {
            return true;
        }
        if (in_array($ability, $user->role->permission->permissions ?? [])) {
            return true;
        }

        return false;
    }
}

if (!function_exists('gate_allows')) {
    /**
     * @param string                     $ability
     * @param mixed                      $parameters
     * @param Authenticatable|Model|null $user
     *
     * @return bool
     */
    function gate_allows(string $ability, $parameters = [], ?Model $user = null): bool
    {
        $user = get_user($user);
        return isset($user)
            ? Gate::forUser($user)->allows($ability, $parameters)
            : Gate::allows($ability, $parameters);
    }
}

if (!function_exists('gate_authorize')) {
    /**
     * @param string                     $ability
     * @param mixed                      $parameters
     * @param Authenticatable|Model|null $user
     *
     * @return Response
     * @throws AuthorizationException
     */
    function gate_authorize(string $ability, $parameters = [], ?Model $user = null): Response
    {
        $user = get_user($user);
        return isset($user)
            ? Gate::forUser($user)->authorize($ability, $parameters)
            : Gate::authorize($ability, $parameters);
    }
}

if (!function_exists('gate_allows_redirect')) {
    /**
     * @param string                     $ability
     * @param mixed                      $parameters
     * @param Authenticatable|Model|null $user
     * @param string                     $route
     *
     * @return RedirectResponse|bool
     */
    function gate_allows_redirect(string $ability, $parameters = [], ?Model $user = null, string $route = 'dashboard'): RedirectResponse
    {
        $allows = gate_allows($ability, $parameters, $user);
        if ($allows) {
            return false;
        }

        return redirect(
            filter_var($route, FILTER_VALIDATE_URL) ? $route : route($route)
        )->with('danger', __('Unauthorized request'));
    }
}
