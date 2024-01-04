<?php

return [
    'modules' => [
        /**
         * Example:
         * VendorA\ModuleX\Providers\ModuleServiceProvider::class,
         * VendorB\ModuleY\Providers\ModuleServiceProvider::class
         *
         */
        Boilerplate\Auth\Providers\ModuleServiceProvider::class,
        Boilerplate\Media\Providers\ModuleServiceProvider::class,
    ],
    'register_route_models' => true
];
