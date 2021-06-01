<?php

namespace Emberfuse\Scorch\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scorch:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Scorch package';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Publish...
        $this->callSilent('vendor:publish', ['--tag' => 'scorch-config', '--force' => true]);
        $this->callSilent('vendor:publish', ['--tag' => 'rules-config', '--force' => true]);
        $this->callSilent('vendor:publish', ['--tag' => 'scorch-support', '--force' => true]);
        $this->callSilent('vendor:publish', ['--tag' => 'scorch-migrations', '--force' => true]);

        // Finish message...
        $this->info('Scorch installed successfully!');
    }
}
