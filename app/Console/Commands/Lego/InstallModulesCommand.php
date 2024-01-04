<?php

namespace App\Console\Commands\Lego;

use App\Console\Commands\Lego\Traits\CanListModules;
use App\Support\Folder;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class InstallModulesCommand extends Command
{
    use CanListModules;

    protected $signature   =
    'lego:install {module-names* : One or more or the module names specified in the lego:list-modules command}';
    protected $description = 'Install lego modules';

    private $anyModuleInstalled    = false;
    private $documentationMessages = [];

    public function handle()
    {
        $this->readModulesManifestFile();
        if ($this->hasNoModules()) {
            return;
        }

        $moduleNames = $this->argument('module-names');
        $this->installModules($moduleNames);

        if (count($this->documentationMessages) > 0) {
            $this->info("\nRunning composer update");
            echo `composer update`;
            $this->newLine();

            foreach ($this->documentationMessages as $documentationMessage) {
                $this->info($documentationMessage);
            }

            $this->newLine();

            $process = new Process([
                "php artisan optimize:clear",
                "composer dump-autoload"
            ]);

            $process->run();
        }

        return 0;
    }

    private function installModules(array $moduleNames)
    {
        collect($moduleNames)->each(
            fn ($moduleName) => $this->installModule($moduleName)
        );
    }

    private function installModule($moduleName)
    {
        $module = $this->findModule($moduleName);
        if (!$module) {
            $this->warn("Module $moduleName not found, skipping ...");
            return;
        }

        $dependencies = $this->getModuleDependencies($moduleName);
        if (count($dependencies) > 0) {
            $this->info("Installing dependencies for {$moduleName} ...");
            $this->installModules($dependencies);
        }

        $this->createModulesFolderWhenNeeded();

        $installPath = base_path('modules/' . $moduleName);

        if (file_exists($installPath)) {
            $this->warn("Module $moduleName is already installed, skipping ...");
        } else {
            $this->info("\nInstalling $moduleName ...");
            echo `git clone {$module['repo']} {$installPath}`;

            $gitFolder = $installPath . '/.git';
            $this->info("Removing module git folder: $gitFolder ...");
            Folder::deleteRecursively($gitFolder);
            $this->documentationMessages[] =
                "Please refer to {$moduleName}'s documentation at {$module['doc']} for post install requirements.";
        }
    }

    private function createModulesFolderWhenNeeded()
    {
        if (!file_exists(base_path('modules'))) {
            mkdir(base_path('modules'));
        }
    }
}
