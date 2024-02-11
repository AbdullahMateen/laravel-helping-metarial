<?php

namespace AbdullahMateen\LaravelHelpingMaterial\Traits\Media;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Image;

trait ImageTrait
{
    public array $imageExtensions = ['png', 'jpg', 'jpeg', 'bmp', 'gif', 'svg', 'webp'];

    public function storeImage()
    {
        $disk      = $this->disk();
        $path      = $this->path();
        $mediaInfo = $this->mediaInformation;
        $filename = $mediaInfo['final_name'];

        if (!Storage::disk($disk)->directoryExists($path)) {
            File::makeDirectory(storage_path("app/$disk/$path"), 0755, true);
        }

        $mediaData = $this->generateImage($this->media(), $path, $disk, $filename);
        if ($this->thumbnail()) {
            $thumbData = $this->generateImageThumb($this->media(), $path, $disk, $filename);
        }

        return [
            'result'    => true,
            'media'     => $mediaData,
            'thumb'     => $thumbData,
            'type'      => Storage::disk($disk)->mimeType($path !== '' ? "$path/$filename" : $filename), // mime_content_type($storagePath . $fileNameToStore),
            'extension' => strtolower($mediaInfo['extension']),
        ];
    }

    public function generateImage($media, $path, $disk, $fileNameToStore)
    {
        $media->storeAs($path, $fileNameToStore, $disk);

        $path = $path !== '' ? "$path/$fileNameToStore" : $fileNameToStore;
        return [
            'name'  => $fileNameToStore,
            'path'  => Storage::disk($disk)->path($path),
            'size'  => Storage::disk($disk)->size($path),
            'url'   => Storage::disk($disk)->url($path),
        ];
    }

    public function generateImageThumb($media, $path, $disk, $fileNameToStore, $width = 200, $height = 200)
    {
        ini_set('memory_limit', '1000M');

        $fileNameToStore = "thumb_$fileNameToStore";

        if ($this->thumbnail() instanceof \Closure) {
            $media = $this->thumbnail()($media);
        } else {
            $media = Image::make($media)->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        $media->stream();
        $path = $path !== '' ? "$path/" : $path;
        Storage::disk($disk)->put($path . $fileNameToStore, $media);

        $path = $path !== '' ? "$path/$fileNameToStore" : $fileNameToStore;
        return [
            'name'  => $fileNameToStore,
            'path'  => Storage::disk($disk)->path($path),
            'size'  => Storage::disk($disk)->size($path),
            'url'   => Storage::disk($disk)->url($path),
        ];
    }

    public static function DeleteImage($disk, $path, $name = '')
    {
        return Storage::disk($disk)->delete($path . $name);
    }
}
