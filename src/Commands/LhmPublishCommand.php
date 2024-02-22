<?php

namespace AbdullahMateen\LaravelHelpingMaterial\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class LhmPublishCommand extends Command
{
    private Filesystem $filesystem;
    private string     $prefix;

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
     * @param Filesystem $files
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
        $this->prefix     = base_path('vendor\abdullah-mateen\laravel-helping-metarial');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $options = [
            'All',
            'Enums          => Copy enum files',
            'Helpers        => Copy helper files',
            'Interfaces     => Copy interface files',
            'Middlewares    => Override middleware',
            'Migrations     => Copy migrations',
            'Models         => Override model files',
            'Rules          => Override rule files',
            'Services       => Override service files',
            'Traits         => Override trait files',
            'Resources      => Copy resources',
        ];

        $values = array_unique($this->choice(
            "Which files would you like to publish? You can select multiples using comma (,) e.g. 1,2,3",
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
        $this->filesystem->ensureDirectoryExists(base_path('app/Enums/Media'));
        $this->filesystem->copy("$this->prefix/stubs/Enums/Media/MediaDiskEnum.stub", base_path('app/Enums/Media/MediaDiskEnum.php'));
        $this->filesystem->copy("$this->prefix/stubs/Enums/Media/MediaDiskEnum.stub", base_path('app/Enums/Media/MediaDiskEnum.php'));
        $this->filesystem->copy("$this->prefix/stubs/Enums/Media/MediaTypeEnum.stub", base_path('app/Enums/Media/MediaTypeEnum.php'));

        $this->filesystem->ensureDirectoryExists(base_path('app/Enums/User'));
        $this->filesystem->copy("$this->prefix/stubs/Enums/User/AccountStatusEnum.stub", base_path('app/Enums/User/AccountStatusEnum.php'));
        $this->filesystem->copy("$this->prefix/stubs/Enums/User/GenderEnum.stub", base_path('app/Enums/User/GenderEnum.php'));
        $this->filesystem->copy("$this->prefix/stubs/Enums/User/RoleEnum.stub", base_path('app/Enums/User/RoleEnum.php'));

        $this->filesystem->ensureDirectoryExists(base_path('app/Enums'));
        $this->filesystem->copy("$this->prefix/stubs/Enums/StatusEnum.stub", base_path('app/Enums/StatusEnum.php'));

        return base_path('app/Enums');
    }

    public function publishHelpers()
    {
        $this->filesystem->ensureDirectoryExists(base_path('app/Helpers'));
        $this->filesystem->copy("$this->prefix/stubs/Helpers/application.stub", base_path('app/Helpers/application.php'));
        $this->filesystem->copy("$this->prefix/stubs/Helpers/files.stub", base_path('app/Helpers/files.php'));
        $this->filesystem->copy("$this->prefix/stubs/Helpers/general.stub", base_path('app/Helpers/general.php'));
        $this->filesystem->copy("$this->prefix/stubs/Helpers/helpers.stub", base_path('app/Helpers/helpers.php'));
        $this->filesystem->copy("$this->prefix/stubs/Helpers/packages.stub", base_path('app/Helpers/packages.php'));
        $this->filesystem->copy("$this->prefix/stubs/Helpers/user.stub", base_path('app/Helpers/user.php'));
        return base_path('app/Helpers');
    }

    public function publishInterfaces()
    {
        $this->filesystem->ensureDirectoryExists(base_path('app/Interfaces'));
        $this->filesystem->copy("$this->prefix/stubs/Interfaces/ColorsInterface.stub", base_path('app/Interfaces/ColorsInterface.php'));
        return base_path('app/Interfaces');
    }

    public function publishMiddlewares()
    {
        $this->filesystem->ensureDirectoryExists(base_path('app/Http/Middleware/Custom'));
        $this->filesystem->copy("$this->prefix/stubs/Middleware/Custom/AuthorizationMiddleware.stub", base_path('app/Http/Middleware/Custom/AuthorizationMiddleware.php'));
        return base_path('app/Http/Middleware/Custom');
    }

    public function publishMigrations()
    {
        $this->filesystem->ensureDirectoryExists(base_path('database/migrations'));
        $this->filesystem->copy("$this->prefix/src/migrations/2024_02_17_053998_create_media_table.php", base_path('database/migrations/2024_02_17_053998_create_media_table.php'));
        return base_path('database/migrations');
    }

    public function publishModels()
    {
        $this->filesystem->ensureDirectoryExists(base_path('app/Models'));
        $this->filesystem->copy("$this->prefix/stubs/Models/ExtendedModel.stub", base_path('app/Models/ExtendedModel.php'));
        $this->filesystem->copy("$this->prefix/stubs/Models/Media.stub", base_path('app/Models/Media.php'));
        return base_path('app/Models');
    }

    public function publishRules()
    {
        $this->filesystem->ensureDirectoryExists(base_path('app/Rules'));
        $this->filesystem->copy("$this->prefix/stubs/Rules/Throttle.stub", base_path('app/Rules/Throttle.php'));
        return base_path('app/Rules');
    }

    public function publishServices()
    {
        $this->filesystem->ensureDirectoryExists(base_path('app/Services/Media'));
        $this->filesystem->copy("$this->prefix/stubs/Services/Media/MediaService.stub", base_path('app/Services/Media/MediaService.php'));
        return base_path('app/Services');
    }

    public function publishTraits()
    {
        $this->filesystem->ensureDirectoryExists(base_path('app/Traits/Api'));
        $this->filesystem->copy("$this->prefix/stubs/Traits/Api/ApiExceptionHandlerTrait.stub", base_path('app/Traits/Api/ApiExceptionHandlerTrait.php'));
        $this->filesystem->copy("$this->prefix/stubs/Traits/Api/ApiResponseTrait.stub", base_path('app/Traits/Api/ApiResponseTrait.php'));

        $this->filesystem->ensureDirectoryExists(base_path('app/Traits/General/Enum'));
        $this->filesystem->copy("$this->prefix/stubs/Traits/General/Enum/GeneralTrait.stub", base_path('app/Traits/General/Enum/GeneralTrait.php'));

        $this->filesystem->ensureDirectoryExists(base_path('app/Traits/General/Model'));
        $this->filesystem->copy("$this->prefix/stubs/Traits/General/Model/AuthorizationTrait.stub", base_path('app/Traits/General/Model/AuthorizationTrait.php'));
        $this->filesystem->copy("$this->prefix/stubs/Traits/General/Model/Encryptable.stub", base_path('app/Traits/General/Model/Encryptable.php'));
        $this->filesystem->copy("$this->prefix/stubs/Traits/General/Model/ModelFetchTrait.stub", base_path('app/Traits/General/Model/ModelFetchTrait.php'));
        $this->filesystem->copy("$this->prefix/stubs/Traits/General/Model/ScopeTrait.stub", base_path('app/Traits/General/Model/ScopeTrait.php'));
        $this->filesystem->copy("$this->prefix/stubs/Traits/General/Model/UserNotificationsTrait.stub", base_path('app/Traits/General/Model/UserNotificationsTrait.php'));
        $this->filesystem->copy("$this->prefix/stubs/Traits/General/Model/ValidationRulesTrait.stub", base_path('app/Traits/General/Model/ValidationRulesTrait.php'));
        $this->filesystem->copy("$this->prefix/stubs/Traits/General/Model/ValidationTrait.stub", base_path('app/Traits/General/Model/ValidationTrait.php'));

        $this->filesystem->ensureDirectoryExists(base_path('app/Traits/Media'));
        $this->filesystem->copy("$this->prefix/stubs/Traits/Media/ArchiveTrait.stub", base_path('app/Traits/Media/ArchiveTrait.php'));
        $this->filesystem->copy("$this->prefix/stubs/Traits/Media/AudioTrait.stub", base_path('app/Traits/Media/AudioTrait.php'));
        $this->filesystem->copy("$this->prefix/stubs/Traits/Media/DocumentTrait.stub", base_path('app/Traits/Media/DocumentTrait.php'));
        $this->filesystem->copy("$this->prefix/stubs/Traits/Media/ImageTrait.stub", base_path('app/Traits/Media/ImageTrait.php'));
        $this->filesystem->copy("$this->prefix/stubs/Traits/Media/VideoTrait.stub", base_path('app/Traits/Media/VideoTrait.php'));

        return base_path('app/Traits');
    }

    public function publishResources()
    {
        $this->filesystem->ensureDirectoryExists(base_path('resources/sass/utilities'));
        $this->filesystem->copyDirectory("$this->prefix/src/resources/sass/utilities", base_path('resources/sass/utilities'));
        return base_path('resources/sass');
    }

}
