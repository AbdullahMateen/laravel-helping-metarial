<?php

namespace AbdullahMateen\LaravelHelpingMaterial\Services\Media;

use AbdullahMateen\LaravelHelpingMaterial\Models\Media;
use AbdullahMateen\LaravelHelpingMaterial\Traits\Media\ArchiveTrait;
use AbdullahMateen\LaravelHelpingMaterial\Traits\Media\DocumentTrait;
use AbdullahMateen\LaravelHelpingMaterial\Traits\Media\ImageTrait;
use AbdullahMateen\LaravelHelpingMaterial\Traits\Media\VideoTrait;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MediaService
{
    use ImageTrait, VideoTrait, DocumentTrait, ArchiveTrait;

    private static bool $useOriginalName = false;

    public static function getMediaInfo($media = null)
    {
        if (!isset($media)) return null;

        try {
            $fileNameWithExt = $media->getClientOriginalName();
            $fileName        = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            $extension       = $media->getClientOriginalExtension();
            $fileNameToStore = uniqid('', true) . '_' . time() . '.' . $extension;

            return [
                'name'        => $fileName,
                'extension'   => $extension,
                'full_name'   => $fileNameWithExt,
                'unique_name' => $fileNameToStore,
            ];
        } catch (\Exception $exception) {
            return null;
        }
    }

    public static function StoreMedia($media, $disk, $path = '', $generateThumb = true, $isPublic = true)
    {
        $mediaInfo = self::getMediaInfo($media);
        if (in_array(strtolower($mediaInfo['extension']), self::$imageExtensions)) return array_merge(self::StoreImage($media, $disk, $path, $generateThumb, $isPublic), ['media_type' => Media::KEY_CATEGORY_IMAGE]);
        if (in_array(strtolower($mediaInfo['extension']), self::$videoExtensions)) return array_merge(self::StoreVideo($media, $disk, $path, $generateThumb, $isPublic), ['media_type' => Media::KEY_CATEGORY_VIDEO]);
        if (in_array(strtolower($mediaInfo['extension']), self::$documentExtensions)) return array_merge(self::StoreDocument($media, $disk, $path, $generateThumb, $isPublic), ['media_type' => Media::KEY_CATEGORY_DOCUMENT]);
        if (in_array(strtolower($mediaInfo['extension']), self::$archiveExtensions)) return array_merge(self::StoreArchive($media, $disk, $path, $generateThumb, $isPublic), ['media_type' => Media::KEY_CATEGORY_ARCHIVE]);
        return null;
    }

    public static function DeleteMedia($disk, $path, $name = '')
    {
        $path = trim(trim($path, '/'), '\\');
        return Storage::disk($disk)->delete("$path/$name");
    }

    public static function temp($data, $model = null, $disk = Media::KEY_DISK_TEMP, $group = Media::KEY_GROUP_TEMP)
    {
        $media                 = new Media();
        $media->group          = $group;
        $media->category       = $data['media_type'] ?? null;
        $media->mediaable_id   = $model->id ?? null;
        $media->mediaable_type = isset($model) ? get_morphs_maps(get_class($model)) : null;
        $media->media_url      = $data['media']['url'];
        $media->thumb_url      = $data['thumb']['url'] ?? null;
        $media->media_name     = $data['media']['name'];
        $media->thumb_name     = $data['thumb']['name'] ?? null;
        $media->path           = $data['media']['path'];
        $media->type           = $data['type'];
        $media->extension      = $data['extension'];
        $media->media_size     = $data['media']['size'];
        $media->thumb_size     = $data['thumb']['size'] ?? null;
        $media->save();

        return $media;
    }

    public static function Move($modal, $disk, $value, $path = '', $column = 'media_name')
    {
        $media = Media::where($column, '=', $value)->first();

        $path = trim(trim($path, '/'), '\\');
        $from = Media::KEY_DISK_TEMP . "/$media->media_name";
        $to   = $disk . "/$path/$media->media_name";

        if (!Storage::disk($disk)->directoryExists($path)) {
            File::makeDirectory(storage_path("app/$disk/$path"), 0755, true);
        }

        if (!Storage::move($from, $to)) return $modal;

        if (isset($media->thumb_name)) {
            $from = Media::KEY_DISK_TEMP . "/$media->thumb_name";
            $to   = $disk . "/$path/$media->thumb_name";
            Storage::move($from, $to);
        }

        $media->group          = $disk;
        $media->mediaable_id   = $modal->id;
        $media->mediaable_type = get_morphs_maps(get_class($modal));

        $media->media_url = Storage::disk($disk)->url("$path/$media->media_name");
        $media->thumb_url = Storage::disk($disk)->url("$path/$media->thumb_name");
        $media->path      = Storage::disk($disk)->path("$path/$media->media_name");
        $media->save();

        return $media;
    }

    public static function save($data, $model, $disk = null, $group = null)
    {
        $media                 = new Media();
        $media->group          = $group;
        $media->category       = $data['media_type'] ?? null;
        $media->mediaable_id   = $model->id;
        $media->mediaable_type = get_morphs_maps(get_class($model));
        $media->media_url      = $data['media']['url'];
        $media->thumb_url      = $data['thumb']['url'] ?? $data['media']['url'];
        $media->media_name     = $data['media']['name'];
        $media->thumb_name     = $data['thumb']['name'] ?? $data['media']['name'];
        $media->path           = $data['media']['path'];
        $media->type           = $data['type'];
        $media->extension      = $data['extension'];
        $media->media_size     = $data['media']['size'];
        $media->thumb_size     = $data['thumb']['size'] ?? $data['media']['size'];
        $media->save();

        return $media;
    }

    public static function update($data, $media, $disk = null, $group = null)
    {
        $media->group      = $group ?? $media->group;
        $media->category   = $data['media_type'] ?? $media->category;
        $media->media_url  = $data['media']['url'];
        $media->thumb_url  = $data['thumb']['url'] ?? $data['media']['url'];
        $media->media_name = $data['media']['name'];
        $media->thumb_name = $data['thumb']['name'] ?? $data['media']['name'];
        $media->path       = $data['media']['path'];
        $media->type       = $data['type'];
        $media->extension  = $data['extension'];
        $media->media_size = $data['media']['size'];
        $media->thumb_size = $data['thumb']['size'] ?? $data['media']['size'];
        $media->save();

        return $media;
    }



    /**
     * @return bool
     */
    public static function isUseOriginalName(): bool
    {
        return self::$useOriginalName;
    }

    /**
     * @param bool $useOriginalName
     *
     * @return MediaService
     */
    public static function setUseOriginalName(bool $useOriginalName = false): void
    {
        self::$useOriginalName = $useOriginalName;
    }

}
