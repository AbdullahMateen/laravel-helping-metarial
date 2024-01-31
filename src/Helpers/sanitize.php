<?php

if (!function_exists('sanitize_text_editor_text')) {
    function sanitize_text_editor_text($text)
    {
        try {

            $text = trim(preg_replace('/<script\b[^>]*>(.*?)<\/script>/m', "", $text));
            $text = trim(preg_replace('/<(p|div)[^>]*><\/(p|div)[^>]*>/mis', "", $text));
            $text = sanitize_text_editor_search_and_replace($text);

        } catch (Exception $exception) {
        }

        return $text;
    }
}

if (!function_exists('sanitize_text_editor_search_and_replace')) {
    function sanitize_text_editor_search_and_replace($text, $offset = 0)
    {
        try {

            $searchStart       = 'style="';
            $searchEnd         = '">';
            $searchStartLength = strlen($searchStart);

            if ($positionStart = strpos($text, $searchStart, $offset)) {
                $positionEnd       = strpos($text, $searchEnd, $positionStart);
                $substring         = substr($text, ($positionStart + $searchStartLength), ($positionEnd - ($positionStart + $searchStartLength)));
                $substringSanitize = str_replace('"', "'", $substring);
                $text              = str_replace($substring, $substringSanitize, $text);

                $positionStart = $positionStart + 1;
                if (strpos($text, $searchStart, $positionStart)) {
                    $text = sanitize_text_editor_search_and_replace($text, $positionStart);
                }
            }

        } catch (Exception $exception) {
        }

        return $text;
    }
}

if (!function_exists('remove_invalid_html_tags')) {
    function remove_invalid_html_tags($string)
    {
        return trim(preg_replace('/<(p|div|span|small|td|tr|h1|h2|h3|h4|h5|h6)[^>]*><\/(p|div|span|small|td|tr|h1|h2|h3|h4|h5|h6)[^>]*>/mis', "", $string));
    }
}

if (!function_exists('remove_script_tag')) {
    function remove_script_tag($string)
    {
        return trim(preg_replace('/<script\b[^>]*>(.*?)<\/script>/m', "", $string));;
    }
}

if (!function_exists('remove_script_tag_from_string')) {
    function remove_script_tag_from_string($string, $clearEmptyTag = true)
    {
        $string = trim(preg_replace('/<script\b[^>]*>(.*?)<\/script>/m', "", $string));
        if ($clearEmptyTag) $string = trim(preg_replace('/<(p|div)[^>]*><\/(p|div)[^>]*>/mis', "", $string));
        return $string;
    }
}

if (!function_exists('telegram_string_sanitizer')) {
    function telegram_string_sanitizer($string)
    {
        $string = preg_replace('/[_*]/', ' ', $string);
        $string = trim($string);

        return $string;
    }
}
