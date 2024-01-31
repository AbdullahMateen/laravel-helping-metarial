<?php

if (!function_exists('get_enums')) {
    /**
     * @param string $key value,name
     *
     * @return array
     */
    function get_enums(string $key = 'value'): array
    {
        $enums  = [];
        $prefix = 'App\Enums';

        $files = \Illuminate\Support\Facades\File::allFiles(app_path('Enums'));
        foreach ($files as $fi => $file) {
            $value        = null;
            $filename     = $file->getFilenameWithoutExtension();
            $relativePath = str($file->getRelativePath())->replace('/', '\\')->value();
            $path         = empty($relativePath) ? 'General' : $relativePath;

            $class            = str("$prefix\\$relativePath\\$filename")->replace('\\\\', '\\')->value();
            $value[$filename] = $class::toFullArray('cases', $key);
            set_nested_array_value($enums[$fi], $path, $value, '\\');
        }

        return array_merge_recursive(...$enums);
    }
}

if (!function_exists('filesystems_setup')) {
    #[ArrayShape(['disks' => "array", 'links' => "array"])]
    function filesystems_setup(bool $shared = false, string|null $sharedPath = null): array
    {
        $disks  = [];
        $shared = !isset($sharedPath) ? false : $shared;

        if ($shared) {
            $links["$sharedPath/public"]        = storage_path('app/public');
            $links[public_path('media/public')] = $links["$sharedPath/public"];
        } else {
            $links = [public_path('media/public') => storage_path('app/public')];
        }

        foreach (\App\Models\Media::DISKS as $key => $value) {
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
