<?php

if (!function_exists('get_morphs_maps')) {
    function get_morphs_maps($class = null)
    {
        $maps = [
            'app'  => 'app',
            'user' => User::class,
        ];

        if (isset($class)) {
            return array_search($class, $maps);
        }

        return $maps;
    }
}

if (!function_exists('get_level_from_key')) {
    function get_level_from_key($key)
    {
        try {
            return \App\Enums\User\RoleEnum::fromName($key)->value;
            //            return User::LEVELS[$key];
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('get_role_from_key')) {
    function get_role_from_key($key)
    {
        try {
            return \App\Enums\User\RoleEnum::fromName($key)->role();
            // return User::ROLES[User::LEVELS[$key]];
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('get_key_from_level')) {
    function get_key_from_level($level)
    {
        try {
            return \App\Enums\User\RoleEnum::tryFrom($level)->name;
            //            $keys = array_keys(User::LEVELS, $level);
            //            return isset($keys) && !empty($keys) ? $keys[0] : null;
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('get_role_from_level')) {
    function get_role_from_level($level)
    {
        try {
            return \App\Enums\User\RoleEnum::tryFrom($level)->role();
            //            return User::ROLES[$level];
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('get_key_from_role')) {
    function get_key_from_role($role)
    {
        try {
            return \App\Enums\User\RoleEnum::fromRole($role)->name;
            //            $levels = array_keys(User::ROLES, $role);
            //            $level  = isset($levels) && !empty($levels) ? $levels[0] : null;
            //            if ($level == null) return null;
            //            $keys = array_keys(User::LEVELS, $level);
            //            return isset($keys) && !empty($keys) ? $keys[0] : null;
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('get_level_from_role')) {
    function get_level_from_role($role)
    {
        try {
            return \App\Enums\User\RoleEnum::fromRole($role)->value;
            //            return array_search($role, User::ROLES);
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('is_level')) {
    function is_level($level, $user = null)
    {
        if (!isset($level) || empty($level)) return false;

        try {
            if (!isset($user)) {
                $user = auth_check() ? auth_user() : null;
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
    function auth_check()
    {
        try {
            return auth()->check();
        } catch (Exception $exception) {
            return false;
        }
    }
}

if (!function_exists('auth_user')) {
    function auth_user()
    {
        try {
            return auth()->user();
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('auth_id')) {
    function auth_id()
    {
        try {
            return auth()->id();
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('is_me')) {
    function is_me($user)
    {
        try {
            if (!auth_check()) return false;
            if (is_numeric($user)) {
                $user = User::find($user);
            }
            return auth_id() == $user->id ? true : false;
        } catch (Exception $exception) {
            return false;
        }
    }
}

if (!function_exists('get_user')) {
    function get_user($user = null)
    {
        try {
            if (!isset($user)) {
                $user = auth_check() ? auth_user() : null;
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
    function device_token($user)
    {
        try {
            $user = get_user($user);
            return $user->device_token ?? '';
        } catch (Exception $exception) {
            return '';
        }
    }
}

if (!function_exists('is_super_admin')) {
    function is_super_admin($user = null): bool
    {
        try {
            $user = get_user($user);
            return isset($user) ? is_level(\App\Enums\User\RoleEnum::SuperAdmin->value, $user) : false;
        } catch (Exception $exception) {
            return false;
        }
    }
}

if (!function_exists('is_account_blocked')) {
    function is_account_blocked($user = null)
    {
        try {
            $user = get_user($user);
            return isset($user) ? $user->status == User::KEY_STATUS_BLOCKED : true;
        } catch (Exception $exception) {
            return true;
        }
    }
}
