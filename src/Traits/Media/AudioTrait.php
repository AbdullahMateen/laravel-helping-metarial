<?php

namespace AbdullahMateen\LaravelHelpingMaterial\Traits\Media;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Image;

trait AudioTrait
{
    public static $audioExtensions = ['mp3', 'mp4', 'mov', 'webm'];

    public static function StoreAudio($media, $disk, $path = '', $generateThumb = false, $isPublic = true)
    {
        $mediaData = ['isset' => false];
        $thumbData = ['isset' => false];

        if (!isset($media)) return [
            'result'    => false,
            'media'     => null,
            'thumb'     => null,
            'type'      => null,
            'extension' => null,
        ];

        $mediaInfo = self::getMediaInfo($media);
        $filename = self::isUseOriginalName() ? $mediaInfo['full_name'] : $mediaInfo['unique_name'];

        $mediaData = self::GenerateVideo($media, $disk, $path, $filename);
        if ($generateThumb && in_array($mediaInfo['extension'], self::$videoExtensions)) {
            // generate thumb
        }

        return $data = [
            'result'    => true,
            'media'     => $mediaData,
            'thumb'     => $thumbData,
            'type'      => Storage::disk($disk)->mimeType($filename), // mime_content_type($storagePath . $fileNameToStore),
            'extension' => strtolower($mediaInfo['extension']),
        ];
    }

    public static function DeleteAudio($disk, $path, $name = '')
    {
        return Storage::disk($disk)->delete($path . $name);
    }

    public static function GenerateAudio($media, $disk, $path, $fileNameToStore)
    {
        $media->storeAs($path, $fileNameToStore, $disk);

        return $data = [
            'isset' => true,
            'name'  => $fileNameToStore,
            'path'  => Storage::disk($disk)->path($fileNameToStore),
            'size'  => Storage::disk($disk)->size($fileNameToStore),
            'url'   => Storage::disk($disk)->url($fileNameToStore),
        ];
    }
}