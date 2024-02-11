<?php

namespace AbdullahMateen\LaravelHelpingMaterial\Services\Media;

use AbdullahMateen\LaravelHelpingMaterial\Enums\Media\MediaDiskEnum;
use AbdullahMateen\LaravelHelpingMaterial\Enums\Media\MediaTypeEnum;
use AbdullahMateen\LaravelHelpingMaterial\Models\Media;
use AbdullahMateen\LaravelHelpingMaterial\Traits\Media\ArchiveTrait;
use AbdullahMateen\LaravelHelpingMaterial\Traits\Media\AudioTrait;
use AbdullahMateen\LaravelHelpingMaterial\Traits\Media\DocumentTrait;
use AbdullahMateen\LaravelHelpingMaterial\Traits\Media\ImageTrait;
use AbdullahMateen\LaravelHelpingMaterial\Traits\Media\VideoTrait;
use Closure;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;

class MediaService
{
    use ImageTrait, AudioTrait, VideoTrait, DocumentTrait, ArchiveTrait;

    private bool                 $useOriginalName = false;
    private ?MediaTypeEnum       $mediaType       = null;
    private ?UploadedFile        $media           = null;
    private MediaDiskEnum|string $disk            = 'public';
    private string               $path            = '';
    private bool                 $thumbnail       = true;


    /**
     * @return MediaTypeEnum|null
     */
    public function mediaType(): ?MediaTypeEnum
    {
        return $this->mediaType;
    }

    /**
     * @param MediaTypeEnum|null $mediaType
     *
     * @return $this
     */
    public function setMediaType(?MediaTypeEnum $mediaType = null): static
    {
        $this->mediaType = $mediaType;
        return $this;
    }

    /**
     * @param MediaTypeEnum|null $mediaType
     *
     * @return array
     */
    public function allowedExtensions(?MediaTypeEnum $mediaType = null): array
    {
        $mediaType = $mediaType ?? $this->mediaType();
        return match ($mediaType) {
            MediaTypeEnum::Image    => self::$imageExtensions,
            MediaTypeEnum::Audio    => self::$audioExtensions,
            MediaTypeEnum::Video    => self::$videoExtensions,
            MediaTypeEnum::Document => self::$documentExtensions,
            MediaTypeEnum::Archive  => self::$archiveExtensions,
            default                 => [],
        };
    }

    /**
     * @param array|string  $extensions
     * @param MediaTypeEnum $type
     *
     * @return $this
     */
    public function setAllowedExtensions(array|string $extensions, ?MediaTypeEnum $mediaType = null): static
    {
        $extensions = array_unique(array_map('strtolower', is_array($extensions) ? $extensions : explode(',', $extensions)));
        $mediaType  = $mediaType ?? $this->mediaType();
        match ($mediaType) {
            MediaTypeEnum::Image    => self::$imageExtensions = $extensions,
            MediaTypeEnum::Audio    => self::$audioExtensions = $extensions,
            MediaTypeEnum::Video    => self::$videoExtensions = $extensions,
            MediaTypeEnum::Document => self::$documentExtensions = $extensions,
            MediaTypeEnum::Archive  => self::$archiveExtensions = $extensions,
        };
        return $this;
    }

    /**
     * @return bool
     */
    public function useOriginalName(): bool
    {
        return $this->useOriginalName;
    }

    /**
     * @param bool $useOriginalName
     *
     * @return $this
     */
    public function setUseOriginalName(bool $useOriginalName = true): static
    {
        $this->useOriginalName = $useOriginalName;
        return $this;
    }

    /**
     * @return UploadedFile
     */
    public function media(): UploadedFile
    {
        return $this->media;
    }

    /**
     * @param UploadedFile $media
     *
     * @return $this
     */
    public function setMedia(UploadedFile $media): static
    {
        $this->media = $media;
        return $this;
    }

    /**
     * @return MediaDiskEnum|string
     */
    public function disk(): MediaDiskEnum|string
    {
        return $this->disk;
    }

    /**
     * @param MediaDiskEnum|string $disk
     *
     * @return $this
     */
    public function setDisk(MediaDiskEnum|string $disk): static
    {
        $this->disk = $disk;
        return $this;
    }

    /**
     * @return string
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setPath(string $path): static
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return bool
     */
    public function thumbnail(): bool
    {
        return $this->thumbnail;
    }

    /**
     * @param bool $thumbnail
     *
     * @return $this
     */
    public function setThumbnail(bool $thumbnail): static
    {
        $this->thumbnail = $thumbnail;
        return $this;
    }

    /**
     * @param UploadedFile        $media
     * @param Closure|string|null $name you will get 2 parameters <br> 1. $filename: actual file name without extension, <br> 2. $extension: actual file extension, <br> and you will return full name with extension e.g. example.png
     *
     * @return array|null
     */
    public function getMediaInfo(Closure|string $name = null): ?array
    {
        //        if (!isset($media)) {
        //            return null;
        //        }

        try {
            $media           = $this->media();
            $fileNameWithExt = $media->getClientOriginalName();
            $fileName        = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            $extension       = $media->getClientOriginalExtension();
            $uniqueName      = sprintf('%s_%s.%s', uniqid('', true), time(), $extension);
            $fileNameToStore = $this->useOriginalName() ? $fileNameWithExt : $uniqueName;

            if (isset($name)) {
                if ($name instanceof Closure) {
                    $fileNameToStore = $name($fileName, $extension);
                } elseif (is_string($name)) {
                    $fileNameToStore = $name;
                }
            }

            return [
                'original'    => $fileNameWithExt,
                'name'        => $fileName,
                'extension'   => $extension,
                'unique_name' => $uniqueName,
                'final_name'  => $fileNameToStore,
            ];
        } catch (Exception) {
            return null;
        }
    }

    /**
     * @param Closure|null $callback
     *
     * @return $this
     */
    public function intervention(Closure $callback): static
    {
        // $media = Image::make($this->media());
        $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
        $media = $manager->read($this->media());
        $media = $callback($media);
        if (!isset($media)) {
            return $this;
        }

        $this->setMedia($media);
        return $this;
    }

    /**
     * @return array|null
     */
    public function store(): ?array
    {
        $mediaInfo = $this->getMediaInfo($this->media());
        if (in_array(strtolower($mediaInfo['extension']), self::$imageExtensions, true)) {
            return array_merge(self::StoreImage($this->media(), $this->disk(), $this->path(), $this->thumbnail()), ['media_type' => MediaTypeEnum::Image->value]);
        }
        if (in_array(strtolower($mediaInfo['extension']), self::$imageExtensions, true)) {
            return array_merge(self::StoreAudio($this->media(), $this->disk(), $this->path(), $this->thumbnail()), ['media_type' => MediaTypeEnum::Audio->value]);
        }
        if (in_array(strtolower($mediaInfo['extension']), self::$imageExtensions, true)) {
            return array_merge(self::StoreVideo($this->media(), $this->disk(), $this->path(), $this->thumbnail()), ['media_type' => MediaTypeEnum::Video->value]);
        }
        if (in_array(strtolower($mediaInfo['extension']), self::$imageExtensions, true)) {
            return array_merge(self::StoreDocument($this->media(), $this->disk(), $this->path(), $this->thumbnail()), ['media_type' => MediaTypeEnum::Document->value]);
        }
        if (in_array(strtolower($mediaInfo['extension']), self::$imageExtensions, true)) {
            return array_merge(self::StoreArchive($this->media(), $this->disk(), $this->path(), $this->thumbnail()), ['media_type' => MediaTypeEnum::Archive->value]);
        }
        return null;
    }


    //    public function storeMedia($media, $disk, $path = '', $generateThumb = true, $isPublic = true)
    //    {
    //        $mediaInfo = $this->getMediaInfo($media);
    //        if (in_array(strtolower($mediaInfo['extension']), self::$imageExtensions)) return array_merge(self::StoreImage($media, $disk, $path, $generateThumb, $isPublic), ['media_type' => Media::KEY_CATEGORY_IMAGE]);
    //        if (in_array(strtolower($mediaInfo['extension']), self::$videoExtensions)) return array_merge(self::StoreVideo($media, $disk, $path, $generateThumb, $isPublic), ['media_type' => Media::KEY_CATEGORY_VIDEO]);
    //        if (in_array(strtolower($mediaInfo['extension']), self::$documentExtensions)) return array_merge(self::StoreDocument($media, $disk, $path, $generateThumb, $isPublic), ['media_type' => Media::KEY_CATEGORY_DOCUMENT]);
    //        if (in_array(strtolower($mediaInfo['extension']), self::$archiveExtensions)) return array_merge(self::StoreArchive($media, $disk, $path, $generateThumb, $isPublic), ['media_type' => Media::KEY_CATEGORY_ARCHIVE]);
    //        return null;
    //    }
    //
    //    public static function deleteMedia($disk, $path, $name = '')
    //    {
    //        $path = trim(trim($path, '/'), '\\');
    //        return Storage::disk($disk)->delete("$path/$name");
    //    }
    //
    //
    //    public function temp($data, $model = null, $disk = Media::KEY_DISK_TEMP, $group = Media::KEY_GROUP_TEMP)
    //    {
    //        $media                 = new Media();
    //        $media->group          = $group;
    //        $media->category       = $data['media_type'] ?? null;
    //        $media->mediaable_id   = $model->id ?? null;
    //        $media->mediaable_type = isset($model) ? get_morphs_maps(get_class($model)) : null;
    //        $media->media_url      = $data['media']['url'];
    //        $media->thumb_url      = $data['thumb']['url'] ?? null;
    //        $media->media_name     = $data['media']['name'];
    //        $media->thumb_name     = $data['thumb']['name'] ?? null;
    //        $media->path           = $data['media']['path'];
    //        $media->type           = $data['type'];
    //        $media->extension      = $data['extension'];
    //        $media->media_size     = $data['media']['size'];
    //        $media->thumb_size     = $data['thumb']['size'] ?? null;
    //        $media->save();
    //
    //        return $media;
    //    }
    //
    //    public function move($modal, $disk, $value, $path = '', $column = 'media_name')
    //    {
    //        $media = Media::where($column, '=', $value)->first();
    //
    //        $path = trim(trim($path, '/'), '\\');
    //        $from = Media::KEY_DISK_TEMP . "/$media->media_name";
    //        $to   = $disk . "/$path/$media->media_name";
    //
    //        if (!Storage::disk($disk)->directoryExists($path)) {
    //            File::makeDirectory(storage_path("app/$disk/$path"), 0755, true);
    //        }
    //
    //        if (!Storage::move($from, $to)) return $modal;
    //
    //        if (isset($media->thumb_name)) {
    //            $from = Media::KEY_DISK_TEMP . "/$media->thumb_name";
    //            $to   = $disk . "/$path/$media->thumb_name";
    //            Storage::move($from, $to);
    //        }
    //
    //        $media->group          = $disk;
    //        $media->mediaable_id   = $modal->id;
    //        $media->mediaable_type = get_morphs_maps(get_class($modal));
    //
    //        $media->media_url = Storage::disk($disk)->url("$path/$media->media_name");
    //        $media->thumb_url = Storage::disk($disk)->url("$path/$media->thumb_name");
    //        $media->path      = Storage::disk($disk)->path("$path/$media->media_name");
    //        $media->save();
    //
    //        return $media;
    //    }
    //
    //    public function save($data, $model, $disk = null, $group = null)
    //    {
    //        $media                 = new Media();
    //        $media->group          = $group;
    //        $media->category       = $data['media_type'] ?? null;
    //        $media->mediaable_id   = $model->id;
    //        $media->mediaable_type = get_morphs_maps(get_class($model));
    //        $media->media_url      = $data['media']['url'];
    //        $media->thumb_url      = $data['thumb']['url'] ?? $data['media']['url'];
    //        $media->media_name     = $data['media']['name'];
    //        $media->thumb_name     = $data['thumb']['name'] ?? $data['media']['name'];
    //        $media->path           = $data['media']['path'];
    //        $media->type           = $data['type'];
    //        $media->extension      = $data['extension'];
    //        $media->media_size     = $data['media']['size'];
    //        $media->thumb_size     = $data['thumb']['size'] ?? $data['media']['size'];
    //        $media->save();
    //
    //        return $media;
    //    }
    //
    //    public function update($data, $media, $disk = null, $group = null)
    //    {
    //        $media->group      = $group ?? $media->group;
    //        $media->category   = $data['media_type'] ?? $media->category;
    //        $media->media_url  = $data['media']['url'];
    //        $media->thumb_url  = $data['thumb']['url'] ?? $data['media']['url'];
    //        $media->media_name = $data['media']['name'];
    //        $media->thumb_name = $data['thumb']['name'] ?? $data['media']['name'];
    //        $media->path       = $data['media']['path'];
    //        $media->type       = $data['type'];
    //        $media->extension  = $data['extension'];
    //        $media->media_size = $data['media']['size'];
    //        $media->thumb_size = $data['thumb']['size'] ?? $data['media']['size'];
    //        $media->save();
    //
    //        return $media;
    //    }


}
