<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

if (!function_exists('generate_username')) {
    /**
     * @param string $name
     *
     * @return string
     */
    function generate_username(string $name = 'Guest'): string
    {
        return sprintf('%s.%s', Str::slug($name), uniqid('', true));
    }
}

if (!function_exists('generate_email')) {
    /**
     * @param string      $name
     * @param string|null $domain
     *
     * @return string
     */
    function generate_email(string $name, ?string $domain = null): string
    {
        $domain = $domain ?? app_domain();
        return sprintf('%s@%s', generate_username($name), $domain);
    }
}

if (!function_exists('generate_password')) {
    /**
     * @param int    $length
     * @param string $chars
     *
     * @return string
     * @throws \Random\RandomException
     */
    function generate_password(int $length = 12, string $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789`-=~!@#$%^&*()_+,./<>?;:[]{}\|'): string
    {
        $password  = '';
        $maxLength = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, $maxLength)];
        }

        return $password;
    }
}

if (!function_exists('generate_number')) {
    /**
     * @param int $length
     *
     * @return string
     */
    function generate_number(int $length = 10): string
    {
        $numbers = '0123456789';
        if ($length > strlen($numbers)) {
            $numbers = str_repeat($numbers, ($length / strlen($numbers) + 1));
        }
        return substr(str_shuffle($numbers), 0, $length);
    }
}

if (!function_exists('generate_unique_id')) {
    /**
     * @param int $length
     *
     * @return string
     * @throws \Random\RandomException
     */
    function generate_unique_id(int $length = 10): string
    {
        return substr(bin2hex(random_bytes($length)), 0, $length);
    }
}

if (!function_exists('generate_unique_id_model')) {
    /**
     * @param Model  $modal
     * @param string $column
     * @param string $uniqueIdPrefix
     * @param int    $length
     * @param int    $recursive
     *
     * @return string
     * @throws \Random\RandomException
     */
    function generate_unique_id_model(Model $modal, string $column, string $uniqueIdPrefix = '', int $length = 10, int $recursive = 5): string
    {
        $uniqueId = generate_unique_id($length);
        if ($recursive > 0) {
            if ($modal::where($column, '=', "$uniqueIdPrefix$uniqueId")->exists()) {
                $uniqueId = generate_unique_id_model($modal, $column, $uniqueIdPrefix, $length, --$recursive);
            }
        } else {
            $count    = $modal::where($column, 'LIKE', "%$uniqueIdPrefix$uniqueId%")->count();
            $uniqueId .= ++$count;
        }
        return $uniqueId;
    }
}

if (!function_exists('generate_avatar_name')) {
    /**
     * @param string $string
     * @param string $delimiter
     * @param bool   $uppercase
     * @param int    $limit
     *
     * @return string
     */
    function generate_avatar_name(string $string, string $delimiter = ' ', bool $uppercase = true, int $limit = 2): string
    {
        return words_fc($string, $delimiter, $uppercase, $limit);
    }
}

if (!function_exists('generate_avatar')) {
    /**
     * @param string|null $name
     * @param string      $fontColor
     * @param string      $backgroundColor
     *
     * @return string
     */
    function generate_avatar(?string $name = null, string $fontColor = 'ffffff', string $backgroundColor = '293042'): string
    {
        $name = $name ?? get_user()->name ?? 'Anonymous';
        return "https://ui-avatars.com/api/?name=$name&background=$backgroundColor&color=$fontColor";
    }
}

if (!function_exists('generate_gravatar')) {
    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param string     $email The email address
     * @param string|int $s     Size in pixels, defaults to 80px [ 1 - 2048 ]
     * @param string     $d     Default imageset to use [ 404 | mp | identicon | monsterid | wavatar | retro | robohash | blank ]
     * @param string     $r     Maximum rating (inclusive) [ g | pg | r | x ]
     * @param bool       $img   True to return a complete IMG tag False for just the URL
     * @param array      $attr  Optional, additional key/value attributes to include in the IMG tag
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
            foreach ($attr as $key => $val) {
                $url .= ' ' . $key . '="' . $val . '"';
            }
            $url .= ' />';
        }
        return $url;
    }
}

if (!function_exists('random_color_hex_part')) {
    /**
     * @return string
     * @throws \Random\RandomException
     */
    function random_color_hex_part(): string
    {
        return str_pad(dechex(random_int(0, 255)), 2, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('generate_random_color_hex')) {
    /**
     * @return string
     * @throws \Random\RandomException
     */
    function generate_random_color_hex(): string
    {
        return '#' . random_color_hex_part() . random_color_hex_part() . random_color_hex_part();
    }
}

if (!function_exists('generate_git_branch')) {
    /**
     * @param string $type Type Could be [ Fix | Imp | Debug | Func | HotFix | etc. ]
     * @param string $name
     *
     * @return string
     */
    function generate_git_branch(string $type, string $name): string
    {
        /* Todo: Dont know what was i thinking ... will see */
        return '';
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
