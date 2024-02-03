<?php

namespace AbdullahMateen\LaravelHelpingMaterial;

use Illuminate\Support\ServiceProvider;

class LaravelHelpingMaterialServiceProvider extends ServiceProvider
{

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/Helpers/' => app_path('Helpers')
            ], 'lhm-helpers');
        }
    }


    public function register()
    {

    }
}
