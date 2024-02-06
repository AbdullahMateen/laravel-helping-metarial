<?php

namespace AbdullahMateen\LaravelHelpingMaterial\Traits\Media;


use Illuminate\Support\Facades\Storage;

trait ArchiveTrait
{
    public static $archiveExtensions = ['7z', 's7z', 'apk', 'jar', 'rar', 'tar.gz', 'tgz', 'tarZ', 'tar', 'zip', 'zipx'];

    public static function StoreArchive($media, $disk, $path = '', $generateThumb = false, $isPublic = true)
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

        $mediaData = self::GenerateArchive($media, $disk, $path, $filename);
        if ($generateThumb && in_array($mediaInfo['extension'], self::$archiveExtensions)) {
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

    public static function DeleteArchive($disk, $path, $name = '')
    {
        return Storage::disk($disk)->delete($path . $name);
    }

    public static function GenerateArchive($media, $disk, $path, $fileNameToStore)
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
