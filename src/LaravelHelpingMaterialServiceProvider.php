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

    /**
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {

            $this->publishes([
                __DIR__ . '/Enums/' => app_path('Enums'),
            ], 'lhm-enums');

            $this->publishes([
                __DIR__ . '/Helpers/' => app_path('Helpers'),
            ], 'lhm-helpers');

            $this->publishes([
                __DIR__ . '/Interfaces/' => app_path('Interfaces'),
            ], 'lhm-interfaces');

            $this->publishes([
                __DIR__ . '/Middleware/Custom/' => app_path('/Middleware/Custom'),
            ], 'lhm-middleware');

            $this->publishes([
                __DIR__ . '/migrations/' => app_path('/database/migrations'),
            ], 'lhm-migrations');

            $this->publishes([
                __DIR__ . '/Models/' => app_path('/Models'),
            ], 'lhm-models');

            $this->publishes([
                __DIR__ . '/resources/sass/' => app_path('/resources/sass'),
            ], 'lhm-sass');

            $this->publishes([
                __DIR__ . '/Rules/' => app_path('/Rules'),
            ], 'lhm-rules');

            $this->publishes([
                __DIR__ . '/Services/' => app_path('/Services'),
            ], 'lhm-services');

            $this->publishes([
                __DIR__ . '/Traits/' => app_path('/Traits'),
            ], 'lhm-traits');

        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app['router']->aliasMiddleware('authorize', AuthorizationMiddleware::class);

         Model::preventLazyLoading(!$this->app->isProduction());

         if (function_exists('get_morphs_maps')) {
             Relation::enforceMorphMap(get_morphs_maps());
         }

        $this->bootDirectories();
        $this->bootDirectives();

        $this->app->bind('MediaService', function () {
            return new MediaService();
        });
    }

    /**
     * @return void
     */
    private function bootDirectories(): void
    {
        if (!File::exists(public_path('media'))) {
            File::makeDirectory(public_path('media'), 0777, true);
        }
    }

    /**
     * @return void
     */
    private function bootDirectives(): void
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
