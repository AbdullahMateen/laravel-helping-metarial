<?php

namespace AbdullahMateen\LaravelHelpingMaterial\Traits\Media;


use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait ImageTrait
{
    public static $imageExtensions = ['png', 'jpg', 'jpeg', 'bmp', 'gif', 'svg', 'webp'];

    public static function StoreImage($media, $disk, $path = '', $generateThumb = true, $isPublic = true)
    {
        $path      = trim(trim($path, '/'), '\\');
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

        if (!Storage::disk($disk)->directoryExists($path)) {
            \File::makeDirectory(storage_path("app/$disk/$path"), 0755, true);
        }

        $mediaData = self::GenerateImage($media, $disk, $path, $filename);
        if ($generateThumb && in_array(strtolower($mediaInfo['extension']), self::$imageExtensions)) {
            $thumbData = self::GenerateImageThumb($media, $disk, $path, $filename);
        }

        return $data = [
            'result'    => true,
            'media'     => $mediaData,
            'thumb'     => $thumbData,
            'type'      => Storage::disk($disk)->mimeType($path != '' ? "$path/$filename" : $filename), // mime_content_type($storagePath . $fileNameToStore),
            'extension' => strtolower($mediaInfo['extension']),
        ];
    }

    public static function GenerateImage($media, $disk, $path, $fileNameToStore)
    {
        $media->storeAs($path, $fileNameToStore, $disk);

        return $data = [
            'isset' => true,
            'name'  => $fileNameToStore,
            'path'  => Storage::disk($disk)->path($path != '' ? "$path/$fileNameToStore" : $fileNameToStore),
            'size'  => Storage::disk($disk)->size($path != '' ? "$path/$fileNameToStore" : $fileNameToStore),
            'url'   => Storage::disk($disk)->url($path != '' ? "$path/$fileNameToStore" : $fileNameToStore),
        ];
    }

    public static function GenerateImageThumb($media, $disk, $path, $fileNameToStore, $width = 200, $height = 200, $isPublic = true)
    {
        ini_set('memory_limit', '1000M');
        $fileNameToStore = 'thumb_' . $fileNameToStore;

        $media = Image::make($media)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
        $media->stream();
        $path = $path != '' ? "$path/" : $path;
        Storage::disk($disk)->put($path . $fileNameToStore, $media);

        return $data = [
            'isset' => true,
            'name'  => $fileNameToStore,
            'path'  => Storage::disk($disk)->path($path != '' ? "$path/$fileNameToStore" : $fileNameToStore),
            'size'  => Storage::disk($disk)->size($path != '' ? "$path/$fileNameToStore" : $fileNameToStore),
            'url'   => Storage::disk($disk)->url($path != '' ? "$path/$fileNameToStore" : $fileNameToStore),
        ];
    }

    public static function DeleteImage($disk, $path, $name = '')
    {
        return Storage::disk($disk)->delete($path . $name);
    }
}
