<?php

namespace AbdullahMateen\LaravelHelpingMaterial\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class LhmPublishCommand extends Command
{
    private Filesystem $filesystem;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lhm:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish the laravel helping material files';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;

        $options = [
            'All',
            'Enums',
            'Helpers',
            'Interfaces',
            'Middlewares',
            'Migrations',
            'Models',
            'Rules',
            'Services',
            'Traits',
            'Resources',
        ];

        $values = array_unique($this->choice(
            "Which files would you like to publish? You can select multiples using",
            $options,
            null,
            null,
            true
        ));

        $publishAll = in_array('All', $values, true);
        if ($publishAll) {
            array_shift($options);
            $values = $options;
        }

        foreach ($values as $value) {
            $this->warn("Publishing $value");
            $path = $this->{"publish$value"}();
            $this->info("$value successfully published to '$path'");
        }

        return Command::SUCCESS;
    }

    public function publishEnums()
    {
        $this->filesystem->copy(__DIR__ . '../../stubs/Enums/Media/MediaDiskEnum.stub', base_path('app/Enums/Media/MediaDiskEnum.php'));
        $this->filesystem->copy(__DIR__ . '../../stubs/Enums/Media/MediaDiskEnum.stub', base_path('app/Enums/Media/MediaDiskEnum.php'));
        $this->filesystem->copy(__DIR__ . '../../stubs/Enums/Media/MediaTypeEnum.stub', base_path('app/Enums/Media/MediaTypeEnum.php'));
        $this->filesystem->copy(__DIR__ . '../../stubs/Enums/User/AccountStatusEnum.stub', base_path('app/Enums/User/AccountStatusEnum.php'));
        $this->filesystem->copy(__DIR__ . '../../stubs/Enums/User/GenderEnum.stub', base_path('app/Enums/User/GenderEnum.php'));
        $this->filesystem->copy(__DIR__ . '../../stubs/Enums/User/RoleEnum.stub', base_path('app/Enums/User/RoleEnum.php'));
        $this->filesystem->copy(__DIR__ . '../../stubs/Enums/StatusEnum.stub', base_path('app/Enums/StatusEnum.php'));
        return base_path('app/Enums');
    }

    public function publishHelpers()
    {
        $this->filesystem->copy(__DIR__ . '../../stubs/Helpers/application.stub', base_path('app/Helpers/application.php'));
        $this->filesystem->copy(__DIR__ . '../../stubs/Helpers/files.stub', base_path('app/Helpers/files.php'));
        $this->filesystem->copy(__DIR__ . '../../stubs/Helpers/general.stub', base_path('app/Helpers/general.php'));
        $this->filesystem->copy(__DIR__ . '../../stubs/Helpers/helpers.stub', base_path('app/Helpers/helpers.php'));
        $this->filesystem->copy(__DIR__ . '../../stubs/Helpers/packages.stub', base_path('app/Helpers/packages.php'));
        $this->filesystem->copy(__DIR__ . '../../stubs/Helpers/user.stub', base_path('app/Helpers/user.php'));
        return base_path('app/Helpers');
    }

    public function publishInterfaces()
    {
        $this->filesystem->copy(__DIR__ . '../../stubs/Interfaces/ColorsInterface.stub', base_path('app/Interfaces/ColorsInterface.php'));
        return base_path('app/Interfaces');
    }

    public function publishMiddlewares()
    {
        $this->filesystem->copy(__DIR__ . '../../stubs/Middleware/Custom/AuthorizationMiddleware.stub', base_path('app/Http/Middleware/Custom/AuthorizationMiddleware.php'));
        return base_path('app/Http/Middleware/Custom');
    }

    public function publishMigrations()
    {
        $this->filesystem->copy(__DIR__ . '../migrations/2024_02_17_053998_create_media_table.php', base_path('database/migrations/2024_02_17_053998_create_media_table.php'));
        return base_path('database/migrations');
    }

    public function publishModels()
    {
        $this->filesystem->copy(__DIR__ . '../../stubs/Models/ExtendedModel.stub', base_path('app/Models/ExtendedModel.php'));
        $this->filesystem->copy(__DIR__ . '../../stubs/Models/Media.stub', base_path('app/Models/Media.php'));
        return base_path('app/Models');
    }

    public function publishRules()
    {
        $this->filesystem->copy(__DIR__ . '../../stubs/Rules/Throttle.stub', base_path('app/Rules/Throttle.php'));
        return base_path('app/Rules');
    }

    public function publishServices()
    {
        $this->filesystem->copy(__DIR__ . '../../stubs/Services/Media/MediaService.stub', base_path('app/Services/Media/MediaService.php'));
        return base_path('app/Services');
    }

    public function publishTraits()
    {
        $this->filesystem->copy(__DIR__ . '../../stubs/Traits/Api/ApiExceptionHandlerTrait.stub', base_path('app/Traits/Api/ApiExceptionHandlerTrait.php'));
        $this->filesystem->copy(__DIR__ . '../../stubs/Traits/Api/ApiResponseTrait.stub', base_path('app/Traits/Api/ApiResponseTrait.php'));
        $this->filesystem->copy(__DIR__ . '../../stubs/Traits/General/Enum/GeneralTrait.stub', base_path('app/Traits/General/Enum/GeneralTrait.php'));
        $this->filesystem->copy(__DIR__ . '../../stubs/Traits/General/Model/AuthorizationTrait.stub', base_path('app/Traits/General/Model/AuthorizationTrait.php'));
        $this->filesystem->copy(__DIR__ . '../../stubs/Traits/General/Model/Encryptable.stub', base_path('app/Traits/General/Model/Encryptable.php'));
        $this->filesystem->copy(__DIR__ . '../../stubs/Traits/General/Model/ModelFetchTrait.stub', base_path('app/Traits/General/Model/ModelFetchTrait.php'));
        $this->filesystem->copy(__DIR__ . '../../stubs/Traits/General/Model/ScopeTrait.stub', base_path('app/Traits/General/Model/ScopeTrait.php'));
        $this->filesystem->copy(__DIR__ . '../../stubs/Traits/General/Model/UserNotificationsTrait.stub', base_path('app/Traits/General/Model/UserNotificationsTrait.php'));
        $this->filesystem->copy(__DIR__ . '../../stubs/Traits/General/Model/ValidationRulesTrait.stub', base_path('app/Traits/General/Model/ValidationRulesTrait.php'));
        $this->filesystem->copy(__DIR__ . '../../stubs/Traits/General/Model/ValidationTrait.stub', base_path('app/Traits/General/Model/ValidationTrait.php'));
        $this->filesystem->copy(__DIR__ . '../../stubs/Traits/Media/ArchiveTrait.stub', base_path('app/Traits/Media/ArchiveTrait.php'));
        $this->filesystem->copy(__DIR__ . '../../stubs/Traits/Media/AudioTrait.stub', base_path('app/Traits/Media/AudioTrait.php'));
        $this->filesystem->copy(__DIR__ . '../../stubs/Traits/Media/DocumentTrait.stub', base_path('app/Traits/Media/DocumentTrait.php'));
        $this->filesystem->copy(__DIR__ . '../../stubs/Traits/Media/ImageTrait.stub', base_path('app/Traits/Media/ImageTrait.php'));
        $this->filesystem->copy(__DIR__ . '../../stubs/Traits/Media/VideoTrait.stub', base_path('app/Traits/Media/VideoTrait.php'));
        return base_path('app/Traits');
    }

    public function publishResources()
    {
        $this->filesystem->copy(__DIR__ . '../resources/sass/', base_path('resources/sass'));
        return base_path('resources/sass');
    }

}
