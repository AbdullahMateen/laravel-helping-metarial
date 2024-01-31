<?php

if (!function_exists('generate_password')) {
    function generate_password(int $length = 12): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789`-=~!@#$%^&*()_+,./<>?;:[]{}\|';

        $str = '';
        $max = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) $str .= $chars[random_int(0, $max)];

        return $str;
    }
}

if (!function_exists('generate_username')) {
    function generate_username(string $name = 'Guest'): string
    {
        $username = str($name)->slug('');
        return sprintf('%s.%s', $username, time());
    }
}

if (!function_exists('generate_email')) {
    function generate_email(string $name, ?string $domain = null): string
    {
        $domain = $domain ?? app_domain();
        return sprintf('%s@%s', generate_username($name), $domain);
    }
}

if (!function_exists('generate_number')) {
    function generate_number(int $length = 10): string
    {
        $numbers = '0123456789';
        if ($length > strlen($numbers)) $numbers = str_repeat($numbers, ($length / strlen($numbers) + 1));
        return substr(str_shuffle($numbers), 0, $length);
    }
}

if (!function_exists('generate_unique_id')) {
    function generate_unique_id(int $length = 10): string
    {
        return substr(bin2hex(random_bytes($length)), 0, $length);
    }
}

if (!function_exists('generate_unique_id_model')) {
    function generate_unique_id_model($modal, string $column, string $uniqueIdPrefix = '', int $length = 10, int $recursive = 5): string
    {
        $uniqueId = generate_unique_id($length);
        if ($recursive != 0) {
            if ($modal::where($column, '=', $uniqueIdPrefix . $uniqueId)->exists()) $uniqueId = generate_unique_id_model($length, ($recursive - 1));
        } else {
            $count    = $modal::where($column, 'LIKE', '%' . $uniqueId . '%')->count();
            $uniqueId = $uniqueId . $count;
        }
        return $uniqueId;
    }
}

if (!function_exists('generate_avatar_name')) {
    function generate_avatar_name(string $string, string $delimiter = ' ', bool $uppercase = true, int $limit = 2): string
    {
        return words_fc($string, $delimiter, $uppercase, $limit);
    }
}

if (!function_exists('generate_avatar')) {
    function generate_avatar(?string $name = null, string $color = 'ffffff', string $background = '293042'): string
    {
        $name = $name ?? get_user()->name ?? 'Anonymous';
        return "https://ui-avatars.com/api/?name={$name}&background={$background}&color={$color}";
    }
}

if (!function_exists('generate_gravatar')) {
    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param string $email The email address
     * @param string $s     Size in pixels, defaults to 80px [ 1 - 2048 ]
     * @param string $d     Default imageset to use [ 404 | mp | identicon | monsterid | wavatar | retro | robohash | blank ]
     * @param string $r     Maximum rating (inclusive) [ g | pg | r | x ]
     * @param bool   $img   True to return a complete IMG tag False for just the URL
     * @param array  $attr  Optional, additional key/value attributes to include in the IMG tag
     *
     * @return String containing either just a URL or a complete image tag
     * @source https://gravatar.com/site/implement/images/php/
     */
    function generate_gravatar(string $email, int $s = 80, string $d = 'mp', string $r = 'g', bool $img = false, array $attr = []): string
    {
        $url = 'https://www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= "?s=$s&d=$d&r=$r";
        if ($img) {
            $url = '<img src="' . $url . '"';
            foreach ($attr as $key => $val)
                $url .= ' ' . $key . '="' . $val . '"';
            $url .= ' />';
        }
        return $url;
    }
}

if (!function_exists('generate_git_branch')) {
    function generate_git_branch(string $type, string $name): string
    {
        return sprintf("%s-$type-%s", generate_unique_id(), md5($name));
    }
}

if (!function_exists('random_color_hex_part')) {
    function random_color_hex_part(): string
    {
        return str_pad(dechex(random_int(0, 255)), 2, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('generate_random_color_hex')) {
    function generate_random_color_hex(): string
    {
        return '#' . random_color_hex_part() . random_color_hex_part() . random_color_hex_part();
    }
}
