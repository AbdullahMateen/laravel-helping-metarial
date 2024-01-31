<?php

namespace Abdul\LaravelHelpingMaterial;

use Illuminate\Support\ServiceProvider;

class LaravelHelpingMaterialServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__.'/Helpers/' => app_path('Helpers')
        ], 'lhm-helpers');
    }


    public function register()
    {

    }
}
