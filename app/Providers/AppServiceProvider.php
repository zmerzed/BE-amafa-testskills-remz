<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->mergeStrMacro();
        $this->mergeArrMacro();
        $this->mergeRequestMacro();

        $this->registerModuleLanguageFiles();
    }

    private function mergeStrMacro(): void
    {
        Str::macro('cleanPhoneNumber', function (?string $value) {
            return str_replace('+', '', $value);
        });
    }

    private function mergeArrMacro(): void
    {
        Arr::macro('snakeKeys', function ($array, $delimiter = '_') {
            $result = [];
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $value = Arr::snakeKeys($value, $delimiter);
                }
                $result[Str::snake($key, $delimiter)] = $value;
            }
            return $result;
        });
    }

    private function mergeRequestMacro(): void
    {
        Request::macro('perPage', function ($perPage = 10) {
            return (int)request()->input('per_page', request()->input('limit', $perPage));
        });
    }

    private function registerModuleLanguageFiles(): void
    {
        $concordModules = concord()->getModules();

        /** @var \Konekt\Concord\BaseBoxServiceProvider $provider */
        foreach ($concordModules as $moduleName => $provider) {
            $langDirectory = $provider->getBasePath() . '/resources/lang';

            $moduleName = str_replace('Boilerplate.', '', $moduleName);
            if (is_dir($langDirectory)) {
                $this->loadTranslationsFrom($langDirectory, $moduleName);
            }
        }
    }
}
