<?php

use App\Models\Media;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

if (!function_exists('get_enums')) {
    /**
     * @param string $key value,name
     *
     * @return array
     */
    function get_enums(string $key = 'value', $baseEnumFolderPath = 'App\Enums'): array
    {
        $enums  = [];

        $folders = explode('\\', $baseEnumFolderPath);
        $app = array_shift($folders);
        $folders = implode('\\', array_map('ucwords', $folders));

        $files = File::allFiles(app_path($folders));
        foreach ($files as $fi => $file) {
            $value        = null;
            $filename     = $file->getFilenameWithoutExtension();
            $relativePath = Str::replaceArray('/', ['\\'], $file->getRelativePath()); //  str($file->getRelativePath())->replace('/', '\\')->value();
            $path         = empty($relativePath) ? 'General' : $relativePath;

            $class            = Str::replaceArray('\\\\', ['\\'], "$baseEnumFolderPath\\$relativePath\\$filename"); //  str("$baseEnumFolderPath\\$relativePath\\$filename")->replace('\\\\', '\\')->value();
            $value[$filename] = $class::toFullArray('cases', $key);
            set_nested_array_value($enums[$fi], $path, $value, '\\');
        }

        return array_merge_recursive(...$enums);
    }
}

/* Todo: This is a testing function, but you can use it as it is if you want. without changing params */
if (!function_exists('filesystems_setup')) {
    /**
     * @param bool        $shared
     * @param string|null $sharedPath
     *
     * @return array{disks: array, links: array}
     */
    #[ArrayShape(['disks' => "array", 'links' => "array"])]
    function filesystems_setup(bool $shared = false, ?string $sharedPath = null): array
    {
        $disks  = [];
        $shared = isset($sharedPath) && $shared;

        if ($shared) {
            $links["$sharedPath/public"]        = storage_path('app/public');
            $links[public_path('media/public')] = $links["$sharedPath/public"];
        } else {
            $links = [public_path('media/public') => storage_path('app/public')];
        }

        foreach (Media::DISKS as $key => $value) {
            $disks[$key] = [
                'driver'     => 'local',
                'root'       => storage_path("app/{$key}"),
                'url'        => app_asset_url() . "/media/{$value}",
                'visibility' => 'public',
            ];

            if ($shared) {
                $links["$sharedPath/$key"]           = storage_path('app/' . $value);
                $links[public_path('media/' . $key)] = $links["$sharedPath/$key"];
            } else {
                $links[public_path('media/' . $key)] = storage_path('app/' . $value);
            }
        }

        return [
            'disks' => $disks,
            'links' => $links,
        ];
    }
}
