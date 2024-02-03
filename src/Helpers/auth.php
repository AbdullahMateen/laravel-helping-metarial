<?php

use App\Enums\User\RoleEnum;

if (!function_exists('get_level_from_key')) {
    /**
     * @param string $key
     *
     * @return null
     */
    function get_level_from_key(string $key)
    {
        try {
            return RoleEnum::fromName($key)->value;
            // return User::LEVELS[$key];
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('get_role_from_key')) {
    /**
     * @param string $key
     *
     * @return null
     */
    function get_role_from_key(string $key)
    {
        try {
            return RoleEnum::fromName($key)->role();
            // return User::ROLES[User::LEVELS[$key]];
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('get_key_from_level')) {
    /**
     * @param string|int $level
     *
     * @return null
     */
    function get_key_from_level($level)
    {
        try {
            return RoleEnum::tryFrom($level)->name;
            // $keys = array_keys(User::LEVELS, $level);
            // return isset($keys) && !empty($keys) ? $keys[0] : null;
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('get_role_from_level')) {
    /**
     * @param string|int $level
     *
     * @return null
     */
    function get_role_from_level($level)
    {
        try {
            return RoleEnum::tryFrom($level)->role();
            // return User::ROLES[$level];
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('get_key_from_role')) {
    /**
     * @param string $role
     *
     * @return null
     */
    function get_key_from_role(string $role)
    {
        try {
            return RoleEnum::fromRole($role)->name;
            // $levels = array_keys(User::ROLES, $role);
            // $level  = isset($levels) && !empty($levels) ? $levels[0] : null;
            // if ($level == null) return null;
            // $keys = array_keys(User::LEVELS, $level);
            // return isset($keys) && !empty($keys) ? $keys[0] : null;
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('get_level_from_role')) {
    /**
     * @param string $role
     *
     * @return null
     */
    function get_level_from_role(string $role)
    {
        try {
            return RoleEnum::fromRole($role)->value;
            // return array_search($role, User::ROLES);
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('is_level')) {
    /**
     * @param             $level
     * @param             $user
     * @param string|null $guard
     *
     * @return false
     */
    function is_level($level, $user = null, ?string $guard = null): bool
    {
        if (!isset($level) || empty($level)) return false;

        try {
            if (!isset($user)) {
                $user = auth_user($guard);
            } else if (is_numeric($user)) {
                $user = User::find($user);
            }
            return $user->isLevel($level);
        } catch (Exception $exception) {
            return false;
        }
    }
}

if (!function_exists('auth_check')) {
    /**
     * @param string|null $guard
     *
     * @return bool
     */
    function auth_check(?string $guard = null): bool
    {
        try {
            return auth($guard)->check();
        } catch (Exception $exception) {
            return false;
        }
    }
}

if (!function_exists('auth_user')) {
    /**
     * @param string|null $guard
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    function auth_user(?string $guard = null): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        try {
            return auth($guard)->user();
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('auth_id')) {
    /**
     * @param string|null $guard
     *
     * @return int|string|null
     */
    function auth_id(?string $guard = null)
    {
        try {
            return auth($guard)->id();
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('is_me')) {
    /**
     * @param $user
     * @param string|null $guard
     *
     * @return bool
     */
    function is_me($user, ?string $guard = null): bool
    {
        try {
            if (!auth_check($guard)) return false;
            if (is_numeric($user)) {
                $user = User::find($user);
            }
            return auth_id($guard) == $user->id;
        } catch (Exception $exception) {
            return false;
        }
    }
}

if (!function_exists('get_user')) {
    /**
     * @param $user
     * @param string|null $guard
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|User|null
     */
    function get_user($user = null, ?string $guard = null)
    {
        try {
            if (!isset($user)) {
                $user = auth_user($guard);
            } elseif (is_numeric($user)) {
                $user = User::find($user);
            }

            return $user;
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('device_token')) {
    /**
     * @param $user
     * @param string|null $guard
     *
     * @return string
     */
    function device_token($user, ?string $guard = null): string
    {
        try {
            $user = get_user($user, $guard);
            return $user->device_token ?? '';
        } catch (Exception $exception) {
            return '';
        }
    }
}

if (!function_exists('is_super_admin')) {
    /**
     * @param $user
     * @param string|null $guard
     *
     * @return bool
     */
    function is_super_admin($user = null, ?string $guard = null): bool
    {
        try {
            $user = get_user($user, $guard);
            return isset($user) ? is_level(RoleEnum::SuperAdmin->value, $user) : false;
        } catch (Exception $exception) {
            return false;
        }
    }
}

if (!function_exists('is_account_blocked')) {
    /**
     * @param             $user
     * @param string|null $guard
     *
     * @return bool
     */
    function is_account_blocked($user = null, ?string $guard = null): bool
    {
        try {
            $user = get_user($user, $guard);
            return isset($user) ? $user->status == User::KEY_STATUS_BLOCKED : true;
        } catch (Exception $exception) {
            return true;
        }
    }
}
