<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

if (!function_exists('send_fcm_notification')) {
    /**
     * @param string $deviceToken
     * @param        $notification
     * @param array  $data
     *
     * @return bool|string
     */
    function send_fcm_notification(string $deviceToken, $notification, array $data = [])
    {
        $accessToken = config('services.notification.token');
        $URL         = config('services.notification.base_url');

        $keys = '';
        foreach ($data['keys'] ?? [] as $key => $value) $keys .= sprintf('"%s": "%s",', $key, is_array($value) ? implode(',', $value) : $value);
        $keys = rtrim($keys, ",");
        if (!empty($keys)) $keys = ',' . $keys;

        $post_data = '{
           "notification":{
              "title":"' . ($notification->title ?? '') . '",
              "body":"' . $notification->message . '",
              "image":"",
              "sound":"default",
              "android_channel_id":"fcm_default_channel"
           },
           "priority":"high",
           "data":{
              "click_action":"FLUTTER_NOTIFICATION_CLICK",
              "notification_id":"' . $notification->id . '",
              "model_id":"' . (is_array($notification->model_id) ? implode(',', $notification->model_id) : $notification->model_id) . '",
              "key":"' . $notification->for . '"
              ' . $keys . '
           },
           "android":{
              "priority":"high",
              "notification":{
                 "title":"' . ($notification->title ?? '') . '",
                 "body":"' . $notification->message . '",
                 "sound":"default"
              }
           },
           "apns":{
              "aps":{
                 "alert":{
                    "title":"' . ($notification->title ?? '') . '",
                    "body":"' . $notification->message . '"
                 },
                 "badge":1
              },
              "headers":{
                 "apns-priority":10
              },
              "payload":{
                 "aps":{
                    "sound":"default"
                 }
              },
              "fcm_options":{
                 "image":""
              },
              "customKey":"customValue"
              ' . $keys . '
           },
           "time_to_live":3600,
           "to":"' . $deviceToken . '"
        }';

        $crl = curl_init();

        $headers   = [];
        $headers[] = 'Content-type: application/json';
        $headers[] = 'Authorization: key=' . $accessToken;
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($crl, CURLOPT_URL, $URL);
        curl_setopt($crl, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($crl, CURLOPT_POST, true);
        curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($crl);

        curl_close($crl);

        //        logs()->info('fcm:result::' . $notification->title, [
        //            'rest'         => json_decode($result),
        //            'notification' => [
        //                'id'        => $notification->id,
        //                'title'     => $notification->title,
        //                'message'   => $notification->message,
        //                'for'       => $notification->for,
        //                'model_id'  => is_array($notification->model_id) ? implode(',', $notification->model_id) : $notification->model_id,
        //                'keys'      => $keys,
        //                'post_data' => '
        //                    "data":{
        //                      "click_action":"FLUTTER_NOTIFICATION_CLICK",
        //                      "notification_id":"' . $notification->id . '",
        //                      "model_id":"' . (is_array($notification->model_id) ? implode(',', $notification->model_id) : $notification->model_id) . '",
        //                      "key":"' . $notification->for . '"
        //                      ' . $keys . '
        //                   }
        //                ',
        //            ],
        //            'device_token' => $deviceToken,
        //        ]);

        return $result;
    }
}

if (!function_exists('get_lat_lng_from_address')) {
    /**
     * @param string $address
     *
     * @return array|null
     */
    function get_lat_lng_from_address(string $address): ?array
    {
        $apiKey = config('services.google.map.api_key');

        try {
            $address = str_replace(" ", "+", $address);

            $json = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$apiKey}");
            $json = json_decode($json);

            if (isset($json->{'results'}) && count($json->{'results'}) > 0) {
                $lat  = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
                $long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
                return ['lat' => $lat, 'lng' => $long];
            }

            return null;
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('lat_long_dist_of_two_points')) {
    /**
     * @param $latitudeFrom
     * @param $longitudeFrom
     * @param $latitudeTo
     * @param $longitudeTo
     * @param $earthRadius
     *
     * @return float|int
     */
    function lat_long_dist_of_two_points($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 3959)
    {
        if (($latitudeFrom == $latitudeTo) && ($longitudeFrom == $longitudeTo)) {
            return 0;
        }

        /* -------------------- Method 1 -------------------- */
        //        $theta = $longitudeFrom - $longitudeTo;
        //        $dist  = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) + cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
        //        $dist  = acos($dist);
        //        $dist  = rad2deg($dist);
        //        $miles = $dist * 60 * 1.1515;
        //        $unit  = strtoupper($unit);
        //
        //        if ($unit == "K") {
        //            return ($miles * 1.609344);
        //        } else if ($unit == "N") {
        //            return ($miles * 0.8684);
        //        } else {
        //            return $miles;
        //        }

        /* -------------------- Method 2 -------------------- */
        // 3959 = result in miles, 6371000 = result in meters
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo   = deg2rad($latitudeTo);
        $lonTo   = deg2rad($longitudeTo);

        $lonDelta = $lonTo - $lonFrom;
        // $a        = pow(cos($latTo) * sin($lonDelta), 2) + pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $a = ((cos($latTo) * sin($lonDelta)) ** 2) + ((cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta)) ** 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);
        return $angle * $earthRadius;

        /* -------------------- Method 3 -------------------- */
        //        $pi = pi();
        //        $x  = sin($latitudeFrom * $pi / 180) *
        //            sin($latitudeTo * $pi / 180) +
        //            cos($latitudeFrom * $pi / 180) *
        //            cos($latitudeTo * $pi / 180) *
        //            cos(($longitudeTo * $pi / 180) - ($longitudeFrom * $pi / 180));
        //        $x  = atan((sqrt(1 - pow($x, 2))) / $x);
        //        return abs((1.852 * 60.0 * (($x / $pi) * 180)) / 1.609344);
    }
}

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

if (!function_exists('secret_value')) {
    /**
     * @param string $string
     * @param array  $display
     * @param bool   $displayBetween
     * @param string $char
     *
     * @return string
     */
    function secret_value(string $string, array $display = [4, -4], bool $displayBetween = false, string $char = '*'): string
    {
        $length = strlen($string);

        $display    = is_array($display) ? $display : [4, -4];
        $display[0] = $display[0] ?? 4;
        $display[1] = $display[1] ?? -4;
        $display[1] = is_negative($display[1]) ? $display[1] : -1 * $display[1];

        $display[1] = is_zero($display[1]) ? -$length : $display[1];

        if ($displayBetween) {
            $mask_number = str_repeat($char, abs($display[0])) . substr($string, abs($display[0]), $display[1]) . str_repeat($char, abs($display[1]));
        } else {
            $lengthHidden = $length - (abs($display[0]) + abs($display[1]));
            $mask_number  = substr($string, 0, abs($display[0])) . str_repeat($char, $lengthHidden) . substr($string, abs($display[0]) + $lengthHidden);
        }

        return $mask_number;
    }
}

if (!function_exists('words_fc')) {
    /**
     * @param string $string
     * @param string $delimiter
     * @param bool   $uppercase
     * @param int    $limit
     *
     * @return string
     */
    function words_fc(string $string, string $delimiter = ' ', bool $uppercase = true, int $limit = 0): string
    {
        $words = explode($delimiter, trim($string));

        $limit = $limit > 0 ? $limit : count($words);
        $limit = count($words) < $limit ? count($words) : $limit;

        $acronym = '';
        for ($i = 0; $i < $limit; $i++) {
            $acronym .= $words[$i][0];
        }

        return $uppercase ? strtoupper($acronym) : strtolower($acronym);
    }
}

if (!function_exists('take_words')) {
    /**
     * @param string $string
     * @param int    $count
     * @param string $end
     *
     * @return string
     */
    function take_words(string $string, int $count = 2, string $end = '...'): string
    {
        return Str::words($string, $count, $end); // str($string)->words($count, '')->trim();
    }
}

if (!function_exists('days_list')) {
    /**
     * @return array{monday: string, tuesday: string, wednesday: string, thursday: string, friday: string, saturday: string, sunday: string}
     */
    function days_list(): array
    {
        return [
            'monday'    => 'Monday',
            'tuesday'   => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday'  => 'Thursday',
            'friday'    => 'Friday',
            'saturday'  => 'Saturday',
            'sunday'    => 'Sunday',
        ];
    }
}

if (!function_exists('months_list')) {
    /**
     * @return string[]
     */
    function months_list(): array
    {
        return [
            'jan' => 'January',
            'feb' => 'February',
            'mar' => 'March',
            'apr' => 'April',
            'may' => 'May',
            'jun' => 'June',
            'jul' => 'July',
            'aug' => 'August',
            'sep' => 'September',
            'oct' => 'October',
            'nov' => 'November',
            'dec' => 'December',
        ];
    }
}

if (!function_exists('camel_case')) {
    /**
     * @param string $string
     * @param bool   $capitalizeFirstCharacter
     *
     * @return array|string|string[]
     */
    function camel_case(string $string, bool $capitalizeFirstCharacter = false)
    {

        $string = str_replace(['-', '_'], ' ', $string);
        $string = ucwords($string);
        $string = str_replace(' ', '', $string);

        if (!$capitalizeFirstCharacter) {
            $string[0] = strtolower($string[0]);
        }

        return $string;
    }
}

if (!function_exists('snake_case')) {
    /**
     * @param string $string
     *
     * @return string
     */
    function snake_case(string $string): string
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $string, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
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

if (!function_exists('is_email_address')) {
    /**
     * @param string $email
     *
     * @return bool
     */
    function is_email_address(string $email): bool
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $find1 = strpos($email, '@');
            $find2 = strpos($email, '.');
            return ($find1 !== false && $find2 !== false);
        }
        return false;
    }
}

if (!function_exists('replace_array_keys')) {
    /**
     * @param array $array
     *
     * @return array|false
     */
    function replace_array_keys(array $array)
    {
        $replaced_keys = str_replace('_', '-', array_keys($array));
        return array_combine($replaced_keys, $array);
    }
}

if (!function_exists('array_search_recursive')) {
    /**
     * @param array  $haystack
     * @param string $needle
     *
     * @return mixed|null
     */
    function array_search_recursive(array $haystack, string $needle)
    {
        $iterator  = new \RecursiveArrayIterator($haystack);
        $recursive = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($recursive as $key => $value) {
            if ($value === $needle) {
                return $key;
            }
        }

        return null;
    }
}

if (!function_exists('array_search_item')) {
    /**
     * @param array  $haystack
     * @param string $needle
     *
     * @return int|string|null
     */
    function array_search_item(array $haystack, string $needle)
    {
        $iterator = new \RecursiveArrayIterator($haystack);
        foreach ($iterator as $key => $value) {
            if (in_array($needle, $value)) {
                return $key;
            }
        }

        return null;
    }
}

if (!function_exists('html_symbols')) {
    /**
     * @param string|null $name
     *
     * @return mixed|string|string[]
     */
    function html_symbols(?string $name = null)
    {
        try {
            $symbols = [
                'arrow_top'          => '↑',
                'arrow_left'         => '←',
                'arrow_right'        => '→',
                'arrow_bottom'       => '↓',
                'arrow_top_left'     => '↖',
                'arrow_top_right'    => '↗',
                'arrow_bottom_left'  => '↙',
                'arrow_bottom_right' => '↘',
                'copyright'          => '©',
                'registered'         => '®',
                'trademark'          => '™',
                '@'                  => '@',
                'at'                 => '@',
                '&'                  => '&',
                'ampersand'          => '&',
                'check'              => '✓',
                'celsius'            => '℃',
                'fahrenheit'         => '℉',
                'dollar'             => '$',
                'cent'               => '¢',
                'pound'              => '£',
                'euro'               => '€',
                'yen'                => '¥',
                'indian'             => '₹',
                'ruble'              => '₽',
                'yuan'               => '元',
                '+'                  => '+',
                'plus'               => '+',
                'add'                => '+',
                '-'                  => '−',
                'minus'              => '−',
                'subtract'           => '−',
                'dash'               => '−',
                'en'                 => '−',
                '*'                  => '×',
                'asterisk'           => '×',
                'multiply'           => '×',
                '/'                  => '/',
                'division'           => '/',
                'divide'             => '/',
                'forward_slash'      => '/',
                '='                  => '=',
                'equal'              => '=',
                '!='                 => '≠',
                'notequal'           => '≠',
                '<'                  => '<>',
                'lessthan'           => '<>',
                '>'                  => '>',
                'greaterthan'        => '>',
                '!'                  => '!',
                'exclamation'        => '!',
                '?'                  => '?',
                'question'           => '?',
                '--'                 => '—',
                'em'                 => '—',
                'doubledash'         => '—',
                'singleleft'         => '‹',
                'singleright'        => '›',
                'doubleleft'         => '«',
                'doubleright'        => '»',
            ];
            return isset($name) ? $symbols[$name] : $symbols;
        } catch (Exception $exception) {
            return '';
        }
    }
}

if (!function_exists('html_symbol_codes')) {
    /**
     * @param string|null $name
     *
     * @return mixed|string|string[]
     */
    function html_symbol_codes(?string $name = null)
    {
        try {
            $codes = [
                'arrow_top'          => '&#8593;',
                'arrow_left'         => '&#8592;',
                'arrow_right'        => '&#8594;',
                'arrow_bottom'       => '&#8595;',
                'arrow_top_left'     => '&#8598;',
                'arrow_top_right'    => '&#8599;',
                'arrow_bottom_left'  => '&#8601;',
                'arrow_bottom_right' => '&#8600;',
                'copyright'          => '&#169;',
                'registered'         => '&#174;',
                'trademark'          => '&#8482;',
                '@'                  => '&#64;',
                'at'                 => '&#64;',
                '&'                  => '&#38;',
                'ampersand'          => '&#38;',
                'check'              => '&#10003;',
                'celsius'            => '&#8451;',
                'fahrenheit'         => '&#8457;',
                'dollar'             => '&#36;',
                'cent'               => '&#162;',
                'pound'              => '&#163;',
                'euro'               => '&#8364;',
                'yen'                => '&#165;',
                'indian'             => '&#8377;',
                'ruble'              => '&#8381;',
                'yuan'               => '&#20803;',
                '+'                  => '&#43;',
                'plus'               => '&#43;',
                'add'                => '&#43;',
                '-'                  => '&#8722;',
                'minus'              => '&#8722;',
                'subtract'           => '&#8722;',
                'dash'               => '&#8722;',
                'en'                 => '&#8722;',
                '*'                  => '&#215;',
                'asterisk'           => '&#215;',
                'multiply'           => '&#215;',
                '/'                  => '&#247;',
                'division'           => '&#247;',
                'divide'             => '&#247;',
                'forward_slash'      => '&#247;',
                '='                  => '&#61;',
                'equal'              => '&#61;',
                '!='                 => '&#8800;',
                'notequal'           => '&#8800;',
                '<'                  => '&#60;',
                'lessthan'           => '&#60;',
                '>'                  => '&#62;',
                'greaterthan'        => '&#62;',
                '!'                  => '&#33;',
                'exclamation'        => '&#33;',
                '?'                  => '&#63;',
                'question'           => '&#63;',
                '--'                 => '&#8212;',
                'em'                 => '&#8212;',
                'doubledash'         => '&#8212;',
                'singleleft'         => '&#8249;',
                'singleright'        => '&#8250;',
                'doubleleft'         => '&#171;',
                'doubleright'        => '&#187;',
            ];

            return isset($name) ? $codes[$name] : $codes;
        } catch (Exception $exception) {
            return '';
        }
    }
}

if (!function_exists('exception_response')) {
    /**
     * @param $exception
     *
     * @return array|Exception|mixed
     */
    function exception_response($exception)
    {
        try {
            if ($exception instanceof Exception) {
                $exception = [
                    'message' => $exception->getMessage(),
                    'file'    => $exception->getFile() . ' : ' . $exception->getLine(),
                    'code'    => $exception->getCode(),
                ];
                return $exception;
            }
            return $exception;
        } catch (Exception $exception) {
            return $exception;
        }
    }
}

if (!function_exists('pagination_stats')) {
    /**
     * @param $paginationCollection
     * @param $perPage
     *
     * @return array{firstPage: int, lastPage: mixed, currentPage: mixed, perPage: mixed, total: mixed, url_page: mixed, start: float|int|mixed, end: float|int|mixed}
     */
    function pagination_stats($paginationCollection, $perPage = null): array
    {

        $total       = $paginationCollection->total();
        $lastPage    = $paginationCollection->lastPage();
        $perPage     = $perPage ?? $paginationCollection->perPage();
        $currentPage = $paginationCollection->currentPage();

        $page  = $currentPage;
        $start = $page == 1 ? $page : ((($page - 1) * $perPage) + 1);
        $start = $total == 0 ? $total : ($start > $total ? 0 : $start);
        $end   = $start == 0 ? $start : ($total < $perPage ? $total : (min($total, ($page * $perPage))));
        // $total < $perPage ? $total : ($total < ($page * $perPage) ? $total : ($page * $perPage))

        //        $page = !isset(request()->page) ? 1 : (request()->page < 1 ? 1 : request()->page);
        //        $start = $page == 1 ? 1 : ((($page - 1) * $perPage) + 1);
        //        $start = $total == 0 ? 0 : ( $start > $total ? 0 : $start );
        //        $end = $total < $perPage ? ( $total < ($page * $perPage) ? 0 : $total) : ($total < ($page * $perPage) ? $total : ($page * $perPage));

        return [
            'firstPage'   => 1,
            'lastPage'    => $lastPage,
            'currentPage' => $currentPage,
            'perPage'     => $perPage,
            'total'       => $total,
            'url_page'    => request()->page,
            'start'       => $start,
            'end'         => $end,
        ];
    }
}

if (!function_exists('set_nested_array_value')) {
    /**
     * Sets a value in a nested array based on path
     * See https://stackoverflow.com/a/9628276/419887
     *
     * @param array  $array     The array to modify
     * @param string $path      The path in the array
     * @param mixed  $value     The value to set
     * @param string $delimiter The separator for the path
     *
     * @return string The previous value
     */
    function set_nested_array_value(array &$array, string $path, &$value, string $delimiter = '/')
    {
        //    $temp = &$array;
        //    foreach(explode($delimiter, $path) as $key) {
        //        $temp = &$temp[$key];
        //    }
        //    $temp = $value;
        //    unset($temp);
        $pathParts = explode($delimiter, $path);

        $current = &$array;
        foreach ($pathParts as $key) {
            $current = &$current[$key];
        }

        $backup  = $current;
        $current = $value;

        return $backup;
    }
}

if (!function_exists('array_flatten')) {
    /**
     * @param array $array
     *
     * @return array|false
     */
    function array_flatten(array $array)
    {
        if (!is_array($array)) return false;

        $result = [];
        foreach ($array as $key => $value) {
//             if (is_array($value)) $result = array_merge($result, array_flatten($value));
            if (is_array($value)) $result = [...$result, ...array_flatten($value)];
            else $result[] = $value;
        }

        return $result;
    }
}

if (!function_exists('json_to_xml')) {
    /**
     * @param string      $json
     * @param bool        $useFirstKeyAsRootTag
     * @param string|null $path
     *
     * @return bool|string|null
     */
    function json_to_xml(string $json, bool $useFirstKeyAsRootTag = false, ?string $path = null)
    {
        try {
            $array = json_decode($json, true);
            return array_to_xml($array, $useFirstKeyAsRootTag, $path);
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('array_to_xml')) {
    /**
     * @param array       $array
     * @param bool        $useFirstKeyAsRootTag
     * @param string|null $path
     *
     * @return bool|string|null
     */
    function array_to_xml(array $array, bool $useFirstKeyAsRootTag = false, ?string $path = null)
    {
        try {
            $root  = $useFirstKeyAsRootTag ? array_key_first($array) : 'root';
            $array = $useFirstKeyAsRootTag ? $array[$root] : $array;

            $simpleXmlElement = new \SimpleXMLElement(sprintf("<?xml version=\"1.0\"?><%s></%s>", $root, $root));
            array_to_xml_conversion_script($array, $simpleXmlElement);
            return isset($path) ? $simpleXmlElement->asXML($path) : $simpleXmlElement->asXML();
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('xml_to_array')) {
    /**
     * @param             $xml
     * @param string|null $wrap
     *
     * @return array|mixed|null
     */
    function xml_to_array($xml, ?string $wrap = null)
    {
        try {
            $xml         = simplexml_load_string($xml);
            $jsonConvert = json_encode($xml);
            $jsonConvert = json_decode($jsonConvert, true);
            if (isset($wrap)) $finalJson[$wrap] = $jsonConvert;
            else $finalJson = $jsonConvert;
            return $finalJson;
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('xml_to_json')) {
    /**
     * @param             $xml
     * @param string|null $wrap
     *
     * @return false|string|null
     */
    function xml_to_json($xml, ?string $wrap = null)
    {
        try {
            return json_encode(xml_to_array($xml, $wrap));
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('array_to_xml_conversion_script')) {
    /**
     * @param array $array
     * @param       $simpleXmlElement
     *
     * @return null
     */
    function array_to_xml_conversion_script(array $array, &$simpleXmlElement)
    {
        try {
            foreach ($array as $key => $value) {
                if (!is_array($value)) {
                    $simpleXmlElement->addChild("$key", "$value");
                    continue;
                }

                if (is_numeric($key)) {
                    array_to_xml_conversion_script($value, $simpleXmlElement);
                    continue;
                }

                $isAssoc = Arr::isAssoc($value);
                if ($isAssoc) {
                    $subnode = $simpleXmlElement->addChild("$key");
                    array_to_xml_conversion_script($value, $subnode);
                    continue;
                }

                $jump = false;
                foreach ($value as $k => $v) {
                    $key = is_numeric($k) ? $key : $k;
                    if (is_array($v)) {
                        $subnode = $simpleXmlElement->addChild("$key");
                        array_to_xml_conversion_script($v, $subnode);
                        $jump = true;
                    }
                }

                if ($jump) continue;
                array_to_xml_conversion_script($value, $subnode);
            }
            return null;
        } catch (Exception $exception) {
            return null;
        }
    }
}







