<?php

namespace AbdullahMateen\LaravelHelpingMaterial\Traits\Media;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait DocumentTrait
{
    public static $documentExtensions = ['pdf', 'doc', 'docx', 'csv', 'xlx', 'txt', 'pptx', 'divx'];

    public static function StoreDocument($media, $disk, $path = '', $generateThumb = false, $isPublic = true)
    {
        $mediaData = ['isset' => false];
        $thumbData = ['isset' => false];

        if (!isset($media)) return [
            'result'    => false,
            'media'     => $mediaData,
            'thumb'     => $thumbData,
            'type'      => null,
            'extension' => null,
        ];

        $mediaInfo = self::getMediaInfo($media);
        $filename = self::isUseOriginalName() ? $mediaInfo['full_name'] : $mediaInfo['unique_name'];

        $mediaData = self::GenerateDocument($media, $disk, $path, $filename);
        if ($generateThumb && in_array($mediaInfo['extension'], self::$documentExtensions)) {
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

    public static function DeleteDocument($disk, $path, $name = '')
    {
        return Storage::disk($disk)->delete($path . $name);
    }

    public static function GenerateDocument($media, $disk, $path, $fileNameToStore)
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
