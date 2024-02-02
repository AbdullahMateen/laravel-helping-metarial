<?php


if (!function_exists('remove_script_tag')) {
    /**
     * @param string $string
     *
     * @return string
     */
    function remove_script_tag(string $string): string
    {
        return trim(preg_replace('/<script\b[^>]*>(.*?)<\/script>/m', "", $string));
    }
}

if (!function_exists('remove_invalid_html_tags')) {
    /**
     * @param string $string
     *
     * @return string
     */
    function remove_invalid_html_tags(string $string): string
    {
        return trim(preg_replace('/<(p|div|span|small|td|tr|h1|h2|h3|h4|h5|h6)[^>]*><\/(p|div|span|small|td|tr|h1|h2|h3|h4|h5|h6)[^>]*>/mis', "", $string));
    }
}

if (!function_exists('remove_script_tag_from_string')) {
    /**
     * @param string $string
     * @param bool   $clearEmptyTag
     *
     * @return string
     */
    function remove_script_tag_from_string(string $string, bool $clearEmptyTag = true): string
    {
        $string = remove_script_tag($string);
        if ($clearEmptyTag) $string = remove_invalid_html_tags($string);
        return $string;
    }
}

if (!function_exists('sanitize_text_editor_text')) {
    /**
     * @param string $text
     *
     * @return string
     */
    function sanitize_text_editor_text(string $text): string
    {
        try {
            $text = remove_script_tag($text);
            $text = remove_invalid_html_tags($text);
            $text = sanitize_text_editor_search_and_replace($text);
        } catch (Exception $exception) {}

        return $text;
    }
}

if (!function_exists('sanitize_text_editor_search_and_replace')) {
    /**
     * @param string $text
     * @param int    $offset
     *
     * @return string
     */
    function sanitize_text_editor_search_and_replace(string $text, int $offset = 0): string
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
        } catch (Exception $exception) {}

        return $text;
    }
}

if (!function_exists('telegram_string_sanitizer')) {
    /**
     * @param string $string
     *
     * @return string
     */
    function telegram_string_sanitizer(string $string): string
    {
        $string = preg_replace('/[_*]/', ' ', $string);
        $string = trim($string);

        return $string;
    }
}
