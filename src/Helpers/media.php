<?php

if (!function_exists('is_media_type_image')) {
    function is_media_type_image(string $string)
    {
        try {
            if ($string === \App\Models\Media::KEY_CATEGORY_IMAGE) return true;
            if (strpos($string, ' image/') !== false) return true;
            if (in_array(strtolower($string), \App\Services\Media\MediaService::$imageExtensions)) return true;

            return false;
        } catch (Exception $exception) {
            return false;
        }
    }
}

if (!function_exists('is_media_type_video')) {
    function is_media_type_video($string)
    {
        try {
            if ($string === \App\Models\Media::KEY_CATEGORY_VIDEO) return true;
            if (in_array(strtolower($string), \App\Services\Media\MediaService::$videoExtensions)) return true;

            return false;
        } catch (Exception $exception) {
            return false;
        }
    }
}

if (!function_exists('is_media_type_document')) {
    function is_media_type_document($string)
    {
        try {
            if ($string === \App\Models\Media::KEY_CATEGORY_DOCUMENT) return true;
            if (in_array(strtolower($string), \App\Services\Media\MediaService::$documentExtensions)) return true;

            return false;
        } catch (Exception $exception) {
            return false;
        }
    }
}

if (!function_exists('is_media_type_archive')) {
    function is_media_type_archive($string)
    {
        try {
            if ($string === \App\Models\Media::KEY_CATEGORY_ARCHIVE) return true;
            if (in_array(strtolower($string), \App\Services\Media\MediaService::$archiveExtensions)) return true;

            return false;
        } catch (Exception $exception) {
            return false;
        }
    }
}

if (!function_exists('is_media_type_of')) {
    function is_media_type_of($string)
    {
        try {
            if (is_media_type_image($string)) return \App\Models\Media::KEY_CATEGORY_IMAGE;
            if (is_media_type_video($string)) return \App\Models\Media::KEY_CATEGORY_VIDEO;
            if (is_media_type_document($string)) return \App\Models\Media::KEY_CATEGORY_DOCUMENT;
            if (is_media_type_archive($string)) return \App\Models\Media::KEY_CATEGORY_ARCHIVE;

            return null;
        } catch (Exception $exception) {
            return null;
        }
    }
}
