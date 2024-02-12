<?php

namespace AbdullahMateen\LaravelHelpingMaterial\Traits\Media;


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use JetBrains\PhpStorm\ArrayShape;

trait ImageTrait
{
    public function storeImage()
    {
        $disk     = $this->disk();
        $path     = $this->path();
        $fileInfo = $this->fileInformation();
        $filename = $fileInfo['name'];

        if (!Storage::disk($disk)->directoryExists($path)) {
            File::makeDirectory(storage_path("app/$disk/$path"), 0755, true);
        }

        $media     = $this->file ?? $this->file();
        $mediaInfo = $this->generateImage($media, $path, $disk, $filename);
        if ($this->thumbnail()) {
            $thumbInfo = $this->generateImageThumb($media, $path, $disk, $filename);
        }

        return [
            'media'     => $mediaInfo,
            'thumb'     => $thumbInfo ?? null,
            'type'      => Storage::disk($disk)->mimeType(trim("$path/$filename", '/')), // mime_content_type($storagePath . $fileNameToStore),
            'extension' => strtolower($fileInfo['_extension']),
        ];
    }

    /**
     * @param $media
     * @param $path
     * @param $disk
     * @param $filename
     *
     * @return array {name: string, path: string, size: int, url: string}
     * @throws \Exception
     */
    public function generateImage($media, $path, $disk, $filename): array
    {
        match (true) {
            $media instanceof UploadedFile => $media->storeAs($path, $filename, $disk),
            $media instanceof Image        => $media->save(Storage::disk($disk)->path("$path/$filename")),
            default                        => throw new \Exception("Unable to recognize file. file type is [" . (get_debug_type($media)) . "]")
        };

        $path = trim("$path/$filename", '/');
        return [
            'name' => $filename,
            'path' => Storage::disk($disk)->path($path),
            'size' => Storage::disk($disk)->size($path),
            'url'  => Storage::disk($disk)->url($path),
        ];
    }

    public function generateImageThumb($media, $path, $disk, $filename)
    {
        // ini_set('memory_limit', '1000M');

        $filename = "thumb_$filename";

        $media = ImageManager::gd()->read($media);
        $media = match (true) {
            $this->thumbnail() instanceof \Closure => $this->thumbnail()($media),
            default                                => $media->resize(200, 200)
        };

        match (true) {
            $media instanceof UploadedFile => $media->storeAs($path, $filename, $disk),
            $media instanceof Image        => $media->save(Storage::disk($disk)->path("$path/$filename")),
            default                        => throw new \Exception("Unable to recognize file. file type is [" . (get_debug_type($media)) . "]")
        };

        $path = trim("$path/$filename", '/');
        return [
            'name' => $filename,
            'path' => Storage::disk($disk)->path($path),
            'size' => Storage::disk($disk)->size($path),
            'url'  => Storage::disk($disk)->url($path),
        ];
    }

    public function deleteImage($disk, $path, $name = '')
    {
        return Storage::disk($disk)->delete("$path$name");
    }
}
