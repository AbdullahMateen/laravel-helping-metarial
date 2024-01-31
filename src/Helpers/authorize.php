<?php

if (!function_exists('policy_authorization')) {
    function policy_authorization($user, string $ability, \Illuminate\Database\Eloquent\Model|null $model = null): bool
    {
        if ($user->role->isReserved()) return true;

        $permissions = $user->role->permission->permissions ?? [];
        if (in_array($ability, $permissions)) return true;

        return false;
    }
}

if (!function_exists('gate_allows')) {
    function gate_allows(string $ability, $parameters = [], $user = null): bool
    {
        $user = get_user($user);

        return isset($user)
            ? Gate::forUser($user)->allows($ability, $parameters)
            : Gate::allows($ability, $parameters);
    }
}

if (!function_exists('gate_authorize')) {
    function gate_authorize(string $ability, $parameters = [], $user = null): \Illuminate\Auth\Access\Response
    {
        $user = get_user($user);

        return isset($user)
            ? Gate::forUser($user)->authorize($ability, $parameters)
            : Gate::authorize($ability, $parameters);
    }
}

if (!function_exists('gate_allows_redirect')) {
    function gate_allows_redirect(string $ability, $parameters = [], $user = null, string $route = 'dashboard'): ?\Illuminate\Http\RedirectResponse
    {
        return gate_allows($ability, $parameters, $user) ? null : redirect()->route($route)->with('danger', __('Unauthorized request'));
    }
}
