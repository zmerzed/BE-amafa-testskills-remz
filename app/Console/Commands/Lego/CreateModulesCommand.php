<?php

namespace App\Console\Commands\Lego;

use App\Console\Commands\Lego\Traits\CanListModules;
use App\Support\Folder;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class CreateModulesCommand extends Command
{
    use CanListModules;

    protected $signature   =
    'lego:create {module-name : Name of module}';
    protected $description = 'Create new lego module';

    public function handle()
    {
        $moduleName = $this->argument('module-name');
        $this->installModule($moduleName);

        $this->newLine();

        $this->info("Please refer to baseplate's documentation at https://gitlab.com/Boilerplate/baseplate/backend/module-boilerplate/-/blob/main/readme.md for post install requirements.");

        $this->newLine();

        $process = new Process([
            "php artisan optimize:clear",
            "composer dump-autoload"
        ]);

        $process->run();

        return 0;
    }

    private function installModule($moduleName)
    {
        $repositoryUrl = 'git@gitlab.com:Boilerplate/baseplate/backend/module-boilerplate.git';
        $this->createModulesFolderWhenNeeded();

        $installPath = base_path('modules/' . $moduleName);
        if (file_exists($installPath)) {
            $this->warn("Module $moduleName is already installed, skipping ...");
        } else {
            $this->info("\nInstalling $moduleName ...");
            echo `git clone {$repositoryUrl} {$installPath}`;

            $gitFolder = $installPath . '/.git';
            $this->info("Removing module git folder: $gitFolder ...");
            Folder::deleteRecursively($gitFolder);
        }
    }

    private function createModulesFolderWhenNeeded()
    {
        if (!file_exists(base_path('modules'))) {
            mkdir(base_path('modules'));
        }
    }

}
