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
use Illuminate\Support\Traits\Tappable;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

class MediaService
{
    use ImageTrait, AudioTrait, VideoTrait, DocumentTrait, ArchiveTrait, Tappable;

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    private Closure|bool $name = false;

    private string $path = '';

    private MediaDiskEnum|string $disk = 'public';

    private MediaTypeEnum|null $mediaType = null;

    private UploadedFile|Image|string|null $originalFile = null;

    private UploadedFile|Image|string|null $file = null;

    private UploadedFile|Image|string|null $fileThumb = null;

    private Closure|bool $thumbnail = false;

    private array|null $extensions = null;

    private array|null $fileInformation = null;

    private array|null $data = null;


    /*
    |--------------------------------------------------------------------------
    | Setters / Getters
    |--------------------------------------------------------------------------
    */

    /* ==================== name ==================== */

    /**
     * @return Closure|bool
     */
    public function name(): Closure|bool
    {
        return $this->name;
    }

    /**
     * @param Closure|bool $name False: use system generated unique name, <br> True: use original file name <br> Closure(string $filename, string $extension): provide custom filename string
     *
     * @return $this
     */
    public function setName(Closure|bool $name = false): static
    {
        $this->name = $name;
        return $this;
    }

    /* ==================== path ==================== */

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
        $this->path = trim($path, '/\\');
        return $this;
    }

    /* ==================== disk ==================== */

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

    /* ==================== media type ==================== */

    /**
     * @return MediaTypeEnum|null
     */
    public function mediaType(): MediaTypeEnum|null
    {
        return $this->mediaType;
    }

    /**
     * @param MediaTypeEnum|null $mediaType
     *
     * @return $this
     */
    public function setMediaType(MediaTypeEnum|null $mediaType = null): static
    {
        $this->mediaType = $mediaType;
        return $this;
    }

    /* ==================== file ==================== */

    /**
     * @return UploadedFile|Image|string|null
     */
    public function file(): UploadedFile|Image|string|null
    {
        return $this->originalFile;
    }

    /**
     * @param UploadedFile|Image|string|null $originalFile
     *
     * @return $this
     */
    public function setFile(UploadedFile|Image|string|null $originalFile): static
    {
        $this->originalFile = $this->resolveFile($originalFile);
        $this->captureFileInformation()->resolveMediaTypeByExtension();
        return $this;
    }

    /* ==================== thumbnail ==================== */

    /**
     * @return Closure|bool
     */
    public function thumbnail(): Closure|bool
    {
        return $this->thumbnail;
    }

    /**
     * @param Closure|bool $thumbnail False: don't generate thumbnail, <br> True: generate thumbnail with default settings <br> Closure(\Intervention\Image\Interfaces\ImageInterface $media) using Intervention api to generate thumb
     *
     * @return $this
     */
    public function setThumbnail(Closure|bool $thumbnail): static
    {
        $this->thumbnail = $thumbnail;
        return $this;
    }

    /* ==================== allowed extensions ==================== */

    /**
     * @return array
     */
    public function extensions(): array
    {
        return $this->extensions ?? $this->mediaType()?->extensions();
        //        $mediaType = $mediaType ?? $this->mediaType();
        //        return match ($mediaType) {
        //            MediaTypeEnum::Image    => $this->imageExtensions,
        //            MediaTypeEnum::Audio    => self::$audioExtensions,
        //            MediaTypeEnum::Video    => self::$videoExtensions,
        //            MediaTypeEnum::Document => self::$documentExtensions,
        //            MediaTypeEnum::Archive  => self::$archiveExtensions,
        //            default                 => [],
        //        };
    }

    /**
     * @param array|string $extensions
     * @param bool         $merge
     *
     * @return $this
     */
    public function setExtensions(array|string $extensions, bool $merge = false): static
    {
        $this->extensions = $this->refineExtensions($extensions, $merge);
        return $this;
        //        $mediaType  = $mediaType ?? $this->mediaType();
        //        match ($mediaType) {
        //            MediaTypeEnum::Image    => $this->imageExtensions = $extensions,
        //            MediaTypeEnum::Audio    => self::$audioExtensions = $extensions,
        //            MediaTypeEnum::Video    => self::$videoExtensions = $extensions,
        //            MediaTypeEnum::Document => self::$documentExtensions = $extensions,
        //            MediaTypeEnum::Archive  => self::$archiveExtensions = $extensions,
        //        };
    }

    /* ==================== file information ==================== */

    /**
     * @return array|null
     */
    public function fileInformation(): ?array
    {
        return $this->fileInformation;
    }

    /**
     * @return MediaService
     */
    public function captureFileInformation(): static
    {
        try {
            $media           = $this->file();
            $fileNameWithExt = $media->getClientOriginalName();
            $fileName        = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            $extension       = $media->getClientOriginalExtension();
            $uniqueName      = sprintf('%s_%s.%s', uniqid('', true), time(), $extension);

            $name            = $this->name();
            $fileNameToStore = match (true) {
                $name instanceof Closure => $name($fileName, $extension),
                $name === false          => $uniqueName,
                $name                    => $fileNameWithExt,
            };

            $this->fileInformation = [
                '_original'  => $fileNameWithExt,
                '_name'      => $fileName,
                '_extension' => $extension,
                'name'       => $fileNameToStore,
                'unique'     => $uniqueName,
            ];
        } catch (Exception) {
            $this->fileInformation = null;
        }

        return $this;
    }

    /* ==================== intervention ==================== */

    /**
     * @param Closure $callback Closure(\Intervention\Image\Interfaces\ImageInterface $file) using Intervention api to generate file and return file object
     *
     * @return $this
     */
    public function intervention(Closure $callback): static
    {
        if ($this->mediaType() !== MediaTypeEnum::Image) {
            return $this;
        }

        $file = ImageManager::gd()->read($this->file());
        $file = $callback($file);
        if (!isset($file)) {
            return $this;
        }

        $this->file = $file;
        return $this;
    }

    /* ==================== helpers ==================== */

    /**
     * @param Image|string|UploadedFile|null $originalFile
     *
     * @return Image|UploadedFile
     */
    private function resolveFile(Image|string|UploadedFile|null $originalFile): Image|UploadedFile
    {
        return match (true) {
            $originalFile instanceof UploadedFile, $originalFile instanceof Image => $originalFile,
            File::exists($originalFile)                                           => path_to_uploaded_file($originalFile),
            is_valid_url($originalFile)                                           => url_to_uploaded_file($originalFile, 'temporary.png'),
            is_base64_image($originalFile)                                        => base64_to_uploaded_file($originalFile, 'temporary.png'),
        };
    }

    /**
     * @param string|null $extension
     *
     * @return void
     */
    private function resolveMediaTypeByExtension(string $extension = null): void
    {
        $extension = strtolower($extension ?? $this->fileInformation['_extension']);
        $this->setMediaType(match (true) {
            in_array($extension, MediaTypeEnum::Image->extensions(), true) => MediaTypeEnum::Image,
            in_array($extension, self::$audioExtensions)                   => MediaTypeEnum::Audio,
            in_array($extension, self::$videoExtensions)                   => MediaTypeEnum::Video,
            in_array($extension, self::$documentExtensions)                => MediaTypeEnum::Document,
            in_array($extension, self::$archiveExtensions)                 => MediaTypeEnum::Archive,
        });
    }

    /**
     * @param array|string $extensions
     * @param bool         $merge
     *
     * @return array|null
     */
    private function refineExtensions(array|string $extensions, bool $merge = false): array|null
    {
        $extensions = array_unique(
            array_filter(
                array_map('strtolower', is_array($extensions) ? $extensions : explode(',', $extensions))
            )
        );

        if ($merge) {
            $extensions = array_merge($this->mediaType()?->extensions(), $extensions);
        }

        return empty($extensions) ? null : $extensions;
    }

    /**
     * @param bool    $condition
     * @param Closure $callback
     *
     * @return $this
     */
    private function when(bool $condition, Closure $callback): static
    {
        if ($condition) {
            $callback($this);
        }
        return $this;
    }

    /* ==================== store to filesystem ==================== */

    /**
     * @return array|null
     */
    public function storeAs($path = null, $filename = null): ?array
    {
        $fileInfo = $this
            ->when(isset($path), fn () => $this->setPath($path))
            ->when(isset($filename), fn () => $this->setName(fn ($firstname, $extension) => $filename))
            ->captureFileInformation()->fileInformation();

        return match (true) {
            in_array(strtolower($fileInfo['_extension']), $this->extensions(), true) => array_merge($this->storeImage(), ['media_type' => MediaTypeEnum::Image]),
            default                                                                  => null,
        };

        //        if (in_array(strtolower($mediaInfo['extension']), $this->imageExtensions, true)) {
        //            return array_merge($this->storeImage(/*$this->media(), $this->disk(), $this->path(), $this->thumbnail()*/), ['media_type' => MediaTypeEnum::Image->value]);
        //        }
        //        if (in_array(strtolower($mediaInfo['extension']), self::$audioExtensions, true)) {
        //            return array_merge(self::StoreAudio($this->file(), $this->disk(), $this->path(), $this->thumbnail()), ['media_type' => MediaTypeEnum::Audio->value]);
        //        }
        //        if (in_array(strtolower($mediaInfo['extension']), self::$videoExtensions, true)) {
        //            return array_merge(self::StoreVideo($this->file(), $this->disk(), $this->path(), $this->thumbnail()), ['media_type' => MediaTypeEnum::Video->value]);
        //        }
        //        if (in_array(strtolower($mediaInfo['extension']), self::$documentExtensions, true)) {
        //            return array_merge(self::StoreDocument($this->file(), $this->disk(), $this->path(), $this->thumbnail()), ['media_type' => MediaTypeEnum::Document->value]);
        //        }
        //        if (in_array(strtolower($mediaInfo['extension']), self::$archiveExtensions, true)) {
        //            return array_merge(self::StoreArchive($this->file(), $this->disk(), $this->path(), $this->thumbnail()), ['media_type' => MediaTypeEnum::Archive->value]);
        //        }
        //        return null;
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
