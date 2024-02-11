<?php

namespace AbdullahMateen\LaravelHelpingMaterial;

use AbdullahMateen\LaravelHelpingMaterial\Middleware\Custom\AuthorizationMiddleware;
use AbdullahMateen\LaravelHelpingMaterial\Services\Media\MediaService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class LaravelHelpingMaterialServiceProvider extends ServiceProvider
{

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/Helpers/' => app_path('Helpers'),
            ], 'lhm-helpers');
        }
    }

    public function register()
    {
        $this->app['router']->aliasMiddleware('authorize', AuthorizationMiddleware::class);

         Model::preventLazyLoading(!$this->app->isProduction());

        Relation::enforceMorphMap(get_morphs_maps());

        $this->bootDirectories();
        $this->bootDirectives();

        $this->app->bind('MediaService', function () {
            return new MediaService();
        });
    }

    private function bootDirectories()
    {
//        if (!File::exists(public_path('media'))) {
//            File::makeDirectory(public_path('media'), 0777, true);
//        }
    }

    private function bootDirectives()
    {
        Blade::directive('hasError', function ($keys) {
            return "<?php
                \$fields = explode(',', $keys);
                foreach (\$fields as \$key) {
                    if (\$errors->has(\$key)) {
                        echo 'is-invalid';
                        break;
                    }
                }
            ?>";
        });
        Blade::directive('showError', function ($keys) {
            return "<?php
                \$fields = explode(',', $keys);
                foreach (\$fields as \$key) {
                    if (\$errors->has(\$key)) {
                        echo '<span class=\"invalid-feedback d-block\" role=\"alert\"><strong>'. \$errors->first(\$key) .'</strong></span>';
                        break;
                    }
                }
            ?>";
        });
    }
}
