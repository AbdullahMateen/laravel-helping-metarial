<?php

declare(strict_types=1);

use Symfony\Component\Finder\Finder;

$files = Finder::create()
    ->files()
    ->in(__DIR__.'/Helpers')
    ->depth(0)
    ->name('*.php');

foreach ($files as $file) {
    require_once $file;
}

if (\Illuminate\Support\Facades\File::exists(app_path('Helpers'))) {
    $files = Finder::create()
        ->files()
        ->in(app_path('Helpers'))
        ->depth(0)
        ->name('*.php');

    foreach ($files as $file) {
        require_once $file;
    }

}
