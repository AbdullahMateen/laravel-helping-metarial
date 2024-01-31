<?php



if (!function_exists('app_logo')) {
    /**
     * @param $logo  'icon,sm,lg,full,text'
     * @param $theme 'light,dark,blue,red,green'
     *
     * @return string|array
     */
    function app_logo(string $logo, string $theme = 'light'): array|string
    {
        $url = match ($logo) {
            'ic'    => asset("assets/images/$theme/logo-inverse.png"),
            'sm'    => asset("assets/images/$theme/logo-inverse.png"),
            'lg'    => asset("assets/images/$theme/logo-inverse.png"),
            'full'  => asset("assets/images/$theme/logo-inverse.png"),
            'text'  => asset("assets/images/$theme/logo-inverse.png"),
            default => '',
        };

        return $url;
    }
}



if (!function_exists('app_copyright')) {
    function app_copyright(): string
    {
        return sprintf('Copyright © %s %s. All rights reserved', now_now()->format('Y'), app_name());
    }
}

if (!function_exists('app_copyright_long')) {
    function app_copyright_long(): string
    {
        return app_copyright();
    }
}

if (!function_exists('webpage_title')) {
    function webpage_title(string $title, $postfix = true): string
    {
        return $postfix ? $title . ' | ' . app_name() : $title;
    }
}

if (!function_exists('email_subject')) {
    function email_subject(string $subject, bool $showAppName = true): string
    {
        return $showAppName ? sprintf('%s - %s', $subject, app_full_name()) : $subject;
    }
}

if (!function_exists('is_api')) {
    function is_api(\Illuminate\Http\Request|null $request = null, string $header = ''): bool
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
    function secret_value(string $string, $display = [4, -4], bool $displayBetween = false, $char = '*')
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
    function words_fc($string, $delimiter = ' ', $uppercase = true, $limit = 0)
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
    function take_words($string, $count = 2)
    {
        return str($string)->words($count, '')->trim();
    }
}

if (!function_exists('days_list')) {
    function days_list()
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
    function months_list()
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
    function camel_case($string, $capitalizeFirstCharacter = false)
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
    function snake_case($string)
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
    function get_model_table($model)
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
    function is_email_address($email)
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
    function replace_array_keys($array)
    {
        $replaced_keys = str_replace('_', '-', array_keys($array));
        return array_combine($replaced_keys, $array);
    }
}

if (!function_exists('array_search_recursive')) {
    function array_search_recursive(array $haystack, $needle)
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
    function array_search_item(array $haystack, $needle)
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
    function html_symbols($name = null)
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
    function html_symbol_codes($name = null)
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
    function pagination_stats($paginationCollection, $perPage = null)
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
    function set_nested_array_value(&$array, $path, &$value, $delimiter = '/')
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
    function array_flatten($array)
    {
        if (!is_array($array)) return false;

        $result = [];
        foreach ($array as $key => $value) {
            // if (is_array($value)) $result = array_merge($result, array_flatten($value));
            if (is_array($value)) $result = [...$result, ...array_flatten($value)];
            else $result[] = $value;
        }

        return $result;
    }
}

if (!function_exists('json_to_xml')) {
    function json_to_xml($json, $useFirstKeyAsRootTag = false, $path = null)
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
    function array_to_xml($array, $useFirstKeyAsRootTag = false, $path = null)
    {
        try {
            $root  = $useFirstKeyAsRootTag ? array_key_first($array) : 'root';
            $array = $useFirstKeyAsRootTag ? $array[$root] : $array;

            $simpleXmlElement = new \SimpleXMLElement(sprintf("<?xml version=\"1.0\"?><%s></%s>", $root, $root));
            array_to_xml_conversion_script($array, $simpleXmlElement);
            return $result = isset($path) ? $simpleXmlElement->asXML($path) : $simpleXmlElement->asXML();
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('xml_to_array')) {
    function xml_to_array($xml, $wrap = null)
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
    function xml_to_json($xml, $wrap = null)
    {
        try {
            return json_encode(xml_to_array($xml, $wrap));
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('array_to_xml_conversion_script')) {
    function array_to_xml_conversion_script($array, &$simpleXmlElement)
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




if (!function_exists('impersonate_manager')) {
    function impersonate_manager()
    {
        return app('impersonate');
    }
}

if (!function_exists('impersonate_url')) {
    function impersonate_url($user)
    {
        return route('impersonate', $user->id);
    }
}

if (!function_exists('impersonate_leave_url')) {
    function impersonate_leave_url()
    {
        return route('impersonate.leave');
    }
}

if (!function_exists('impersonate_user')) {
    function impersonate_user($user)
    {
        return redirect()->to(impersonate_url($user));
    }
}

if (!function_exists('is_impersonate')) {
    function is_impersonate()
    {
        return impersonate_manager()->isImpersonating();
    }
}

if (!function_exists('leave_impersonate')) {
    function leave_impersonate()
    {
        return impersonate_manager()->leave();
    }
}

if (!function_exists('get_impersonator_id')) {
    function get_impersonator_id()
    {
        return impersonate_manager()->getImpersonatorId();
    }
}





