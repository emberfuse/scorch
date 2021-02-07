<?php

namespace Cratespace\Sentinel\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sentinel:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Sentinel package';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Publish...
        $this->callSilent('vendor:publish', ['--tag' => 'sentinel-config', '--force' => true]);
        $this->callSilent('vendor:publish', ['--tag' => 'rules-config', '--force' => true]);
        $this->callSilent('vendor:publish', ['--tag' => 'sentinel-support', '--force' => true]);
        $this->callSilent('vendor:publish', ['--tag' => 'sentinel-migrations', '--force' => true]);

        // Finish message...
        $this->info('Sentinel installed successfully!');
    }
}
