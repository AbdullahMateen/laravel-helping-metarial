<?php

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;

/*
|--------------------------------------------------------------------------
| Packages Related Helper Functions
|--------------------------------------------------------------------------
*/

/* ==================== Impersonate Manager ==================== */

if (!function_exists('impersonate_manager')) {
    /**
     * @return Application|mixed
     */
    function impersonate_manager(): mixed
    {
        return app('impersonate');
    }
}

if (!function_exists('impersonate_url')) {
    /**
     * @param $user
     *
     * @return string
     */
    function impersonate_url($user): string
    {
        return route('impersonate', $user->id);
    }
}

if (!function_exists('impersonate_leave_url')) {
    /**
     * @return string
     */
    function impersonate_leave_url(): string
    {
        return route('impersonate.leave');
    }
}

if (!function_exists('impersonate_user')) {
    /**
     * @param $user
     *
     * @return RedirectResponse
     */
    function impersonate_user($user): RedirectResponse
    {
        return redirect()->to(impersonate_url($user));
    }
}

if (!function_exists('is_impersonating')) {
    /**
     * @return mixed
     */
    function is_impersonating(): mixed
    {
        return impersonate_manager()->isImpersonating();
    }
}

if (!function_exists('leave_impersonate')) {
    /**
     * @return mixed
     */
    function leave_impersonate(): mixed
    {
        return impersonate_manager()->leave();
    }
}

if (!function_exists('get_impersonator_id')) {
    /**
     * @return mixed
     */
    function get_impersonator_id(): mixed
    {
        return impersonate_manager()->getImpersonatorId();
    }
}
