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
        //        if ($this->app->runningInConsole()) {
        //
        //            $this->publishes([
        //                __DIR__ . '../stubs/Enums/Media/MediaDiskEnum.stub'    => base_path('app/Enums/Media/MediaDiskEnum.php'),
        //                __DIR__ . '../stubs/Enums/Media/MediaTypeEnum.stub'    => base_path('app/Enums/Media/MediaTypeEnum.php'),
        //                __DIR__ . '../stubs/Enums/User/AccountStatusEnum.stub' => base_path('app/Enums/User/AccountStatusEnum.php'),
        //                __DIR__ . '../stubs/Enums/User/GenderEnum.stub'        => base_path('app/Enums/User/GenderEnum.php'),
        //                __DIR__ . '../stubs/Enums/User/RoleEnum.stub'          => base_path('app/Enums/User/RoleEnum.php'),
        //                __DIR__ . '../stubs/Enums/StatusEnum.stub'             => base_path('app/Enums/StatusEnum.php'),
        //            ], 'lhm-enums');
        //
        //            $this->publishes([
        //                __DIR__ . '../stubs/Helpers/application.stub' => base_path('app/Helpers/application.php'),
        //                __DIR__ . '../stubs/Helpers/files.stub'       => base_path('app/Helpers/files.php'),
        //                __DIR__ . '../stubs/Helpers/general.stub'     => base_path('app/Helpers/general.php'),
        //                __DIR__ . '../stubs/Helpers/helpers.stub'     => base_path('app/Helpers/helpers.php'),
        //                __DIR__ . '../stubs/Helpers/packages.stub'    => base_path('app/Helpers/packages.php'),
        //                __DIR__ . '../stubs/Helpers/user.stub'        => base_path('app/Helpers/user.php'),
        //            ], 'lhm-helpers');
        //
        //            $this->publishes([
        //                __DIR__ . '../stubs/Interfaces/ColorsInterface.stub' => base_path('app/Interfaces/ColorsInterface.php'),
        //            ], 'lhm-interfaces');
        //
        //            $this->publishes([
        //                __DIR__ . '../stubs/Middleware/Custom/AuthorizationMiddleware.stub' => base_path('app/Http/Middleware/Custom/AuthorizationMiddleware.php'),
        //            ], 'lhm-middleware');
        //
        //            $this->publishes([
        //                __DIR__ . '/migrations/2024_02_17_053998_create_media_table.php' => base_path('database/migrations/2024_02_17_053998_create_media_table.php'),
        //            ], 'lhm-migrations');
        //
        //            $this->publishes([
        //                __DIR__ . '../stubs/Models/ExtendedModel.stub' => base_path('app/Models/ExtendedModel.php'),
        //                __DIR__ . '../stubs/Models/Media.stub'         => base_path('app/Models/Media.php'),
        //            ], 'lhm-models');
        //
        //            $this->publishes([
        //                __DIR__ . '/resources/sass/' => base_path('resources/sass'),
        //            ], 'lhm-sass');
        //
        //            $this->publishes([
        //                __DIR__ . '../stubs/Rules/Throttle.stub' => base_path('app/Rules/Throttle.php'),
        //            ], 'lhm-rules');
        //
        //            $this->publishes([
        //                __DIR__ . '../stubs/Services/Media/MediaService.stub' => base_path('app/Services/Media/MediaService.php'),
        //            ], 'lhm-services');
        //
        //            $this->publishes([
        //                __DIR__ . '../stubs/Traits/Api/ApiExceptionHandlerTrait.stub'         => base_path('app/Traits/Api/ApiExceptionHandlerTrait.php'),
        //                __DIR__ . '../stubs/Traits/Api/ApiResponseTrait.stub'                 => base_path('app/Traits/Api/ApiResponseTrait.php'),
        //                __DIR__ . '../stubs/Traits/General/Enum/GeneralTrait.stub'            => base_path('app/Traits/General/Enum/GeneralTrait.php'),
        //                __DIR__ . '../stubs/Traits/General/Model/AuthorizationTrait.stub'     => base_path('app/Traits/General/Model/AuthorizationTrait.php'),
        //                __DIR__ . '../stubs/Traits/General/Model/Encryptable.stub'            => base_path('app/Traits/General/Model/Encryptable.php'),
        //                __DIR__ . '../stubs/Traits/General/Model/ModelFetchTrait.stub'        => base_path('app/Traits/General/Model/ModelFetchTrait.php'),
        //                __DIR__ . '../stubs/Traits/General/Model/ScopeTrait.stub'             => base_path('app/Traits/General/Model/ScopeTrait.php'),
        //                __DIR__ . '../stubs/Traits/General/Model/UserNotificationsTrait.stub' => base_path('app/Traits/General/Model/UserNotificationsTrait.php'),
        //                __DIR__ . '../stubs/Traits/General/Model/ValidationRulesTrait.stub'   => base_path('app/Traits/General/Model/ValidationRulesTrait.php'),
        //                __DIR__ . '../stubs/Traits/General/Model/ValidationTrait.stub'        => base_path('app/Traits/General/Model/ValidationTrait.php'),
        //                __DIR__ . '../stubs/Traits/Media/ArchiveTrait.stub'                   => base_path('app/Traits/Media/ArchiveTrait.php'),
        //                __DIR__ . '../stubs/Traits/Media/AudioTrait.stub'                     => base_path('app/Traits/Media/AudioTrait.php'),
        //                __DIR__ . '../stubs/Traits/Media/DocumentTrait.stub'                  => base_path('app/Traits/Media/DocumentTrait.php'),
        //                __DIR__ . '../stubs/Traits/Media/ImageTrait.stub'                     => base_path('app/Traits/Media/ImageTrait.php'),
        //                __DIR__ . '../stubs/Traits/Media/VideoTrait.stub'                     => base_path('app/Traits/Media/VideoTrait.php'),
        //            ], 'lhm-traits');
        //        }
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
