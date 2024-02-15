<?php

namespace AbdullahMateen\LaravelHelpingMaterial\Traits\Media;


use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

trait ImageTrait
{
    /**
     * @return array{media: array, thumb: array|null, type: false|string, extension: string}
     * @throws Exception
     */
    private function storeImage(): array
    {
        $file     = $this->getFile();
        $disk     = $this->getDisk();
        $path     = $this->getPath();
        $fileInfo = $this->fileInformation();
        $filename = $fileInfo['name'];

        if (!Storage::disk($disk)->directoryExists($path)) {
            File::makeDirectory(storage_path("app/$disk/$path"), 0755, true);
        }

        $mediaInfo = $this->generateImage($file, $path, $disk, $filename);
        if ($this->getThumbnail()) {
            $thumbInfo = $this->generateImageThumb($file, $path, $disk, $filename);
        }

        return [
            'media'     => $mediaInfo,
            'thumb'     => $thumbInfo ?? null,
            'type'      => Storage::disk($disk)->mimeType(trim("$path/$filename", '/')), // mime_content_type($storagePath . $fileNameToStore),
            'extension' => strtolower($fileInfo['_extension']),
        ];
    }

    /**
     * @param mixed  $media
     * @param string $path
     * @param string $disk
     * @param string $filename
     *
     * @return array{name: string, path: string, size: int, url: string}
     * @throws Exception
     */
    private function generateImage(mixed $media, string $path, string $disk, string $filename): array
    {
        if (!is_null($this->fileCallback)) {
            $image = ImageManager::gd()->read($media);
            $media = ($this->fileCallback)($image) ?? $media;
            if ($media instanceof Image) {
                $this->setFileMutated($media);
            }
        }
        return $this->saveImage($media, $path, $filename, $disk);
    }

    /**
     * @param mixed  $media
     * @param string $path
     * @param string $disk
     * @param string $filename
     *
     * @return array{name: string, path: string, size: int, url: string}
     * @throws Exception
     */
    private function generateImageThumb(mixed $media, string $path, string $disk, string $filename): array
    {
        $filename = "thumb_$filename";

        if (!($media instanceof Image) && $this->getThumbnail()) {
            $media           = ImageManager::gd()->read($media);
            $media           = $media->scale(200, 200);
            $this->fileThumb = $media;
        }

        if ($this->getThumbnail()) {
            $image = ImageManager::gd()->read($media);
            if (!is_null($this->fileThumbCallback)) {
                $media = ($this->fileThumbCallback)($image) ?? $media;
                if ($media instanceof Image) {
                    $this->setFileThumb($media);
                }
            } else {
                $media = $media->scale(200, 200);
                $this->setFileThumb($media);
            }
        }

        return $this->saveImage($media, $path, $filename, $disk);
    }

    /**
     * @param mixed  $media
     * @param string $path
     * @param string $filename
     * @param string $disk
     *
     * @return array{name: string, path: string, size: int, url: string}
     * @throws Exception
     */
    private function saveImage(mixed $media, string $path, string $filename, string $disk): array
    {
        match (true) {
            $media instanceof UploadedFile => $media->storeAs($path, $filename, $disk),
            $media instanceof Image        => $media->save(Storage::disk($disk)->path("$path/$filename")),
            default                        => throw new Exception("Unable to recognize file. file type is [" . (get_debug_type($media)) . "]")
        };

        $path = trim("$path/$filename", '/');
        return [
            'name' => $filename,
            'path' => Storage::disk($disk)->path($path),
            'size' => Storage::disk($disk)->size($path),
            'url'  => Storage::disk($disk)->url($path),
        ];
    }
}
