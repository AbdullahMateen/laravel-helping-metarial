<?php

use App\Models\Media;
use App\Services\Media\MediaService;

if (!function_exists('is_media_type_image')) {
    /**
     * @param string $string
     *
     * @return bool
     */
    function is_media_type_image(string $string): bool
    {
        try {
            if ($string === Media::KEY_CATEGORY_IMAGE) return true;
            if (strpos($string, ' image/') !== false) return true;
            if (in_array(strtolower($string), MediaService::$imageExtensions)) return true;

            return false;
        } catch (Exception $exception) {
            return false;
        }
    }
}

if (!function_exists('is_media_type_video')) {
    /**
     * @param string $string
     *
     * @return bool
     */
    function is_media_type_video(string $string): bool
    {
        try {
            if ($string === Media::KEY_CATEGORY_VIDEO) return true;
            if (in_array(strtolower($string), MediaService::$videoExtensions)) return true;

            return false;
        } catch (Exception $exception) {
            return false;
        }
    }
}

if (!function_exists('is_media_type_document')) {
    /**
     * @param string $string
     *
     * @return bool
     */
    function is_media_type_document(string $string): bool
    {
        try {
            if ($string === Media::KEY_CATEGORY_DOCUMENT) return true;
            if (in_array(strtolower($string), MediaService::$documentExtensions)) return true;

            return false;
        } catch (Exception $exception) {
            return false;
        }
    }
}

if (!function_exists('is_media_type_archive')) {
    /**
     * @param string $string
     *
     * @return bool
     */
    function is_media_type_archive(string $string): bool
    {
        try {
            if ($string === Media::KEY_CATEGORY_ARCHIVE) return true;
            if (in_array(strtolower($string), MediaService::$archiveExtensions)) return true;

            return false;
        } catch (Exception $exception) {
            return false;
        }
    }
}

if (!function_exists('is_media_type_of')) {
    /**
     * @param string $string
     *
     * @return string|null
     */
    function is_media_type_of(string $string)
    {
        try {
            if (is_media_type_image($string)) return Media::KEY_CATEGORY_IMAGE;
            if (is_media_type_video($string)) return Media::KEY_CATEGORY_VIDEO;
            if (is_media_type_document($string)) return Media::KEY_CATEGORY_DOCUMENT;
            if (is_media_type_archive($string)) return Media::KEY_CATEGORY_ARCHIVE;

            return null;
        } catch (Exception $exception) {
            return null;
        }
    }
}
