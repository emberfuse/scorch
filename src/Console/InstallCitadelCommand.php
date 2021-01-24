<?php

namespace Citadel\Console;

use Illuminate\Support\Str;
use Illuminate\Console\Command;

class InstallCitadelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'citadel:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Citadel package';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Publish...
        $this->callSilent('vendor:publish', ['--tag' => 'citadel-config', '--force' => true]);
        $this->callSilent('vendor:publish', ['--tag' => 'rules-config', '--force' => true]);
        $this->callSilent('vendor:publish', ['--tag' => 'citadel-support', '--force' => true]);
        $this->callSilent('vendor:publish', ['--tag' => 'citadel-migrations', '--force' => true]);

        // Citadel Provider...
        $this->installServiceProviderAfter('RouteServiceProvider', 'CitadelServiceProvider');
    }

    /**
     * Install the service provider in the application configuration file.
     *
     * @param string $after
     * @param string $name
     *
     * @return void
     */
    protected function installServiceProviderAfter(string $after, string $name): void
    {
        if (! Str::contains($appConfig = file_get_contents(config_path('app.php')), 'App\\Providers\\' . $name . '::class')) {
            file_put_contents(config_path('app.php'), str_replace(
                'App\\Providers\\' . $after . '::class,',
                'App\\Providers\\' . $after . '::class,' . \PHP_EOL . '        App\\Providers\\' . $name . '::class,',
                $appConfig
            ));
        }
    }
}
