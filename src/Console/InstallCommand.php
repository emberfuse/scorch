<?php

namespace Cratespace\Citadel\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
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

        // Finish message...
        $this->info('Citadel installed successfully!');
    }
}
