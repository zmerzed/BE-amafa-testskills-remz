<?php

return [
    'modules' => [],
    'routes' => [
        [
            'files' => ['api'],
            'prefix' => 'api/v1',
            'middleware' => ['api'],
        ],
        [
            'files' => ['web'],
            'middleware' => ['web'],
        ],
    ],
    'views' => [
        'namespace' => 'auth',
    ],

    'use_bypass_code' => (bool)env('APP_USE_BYPASS_CODE', false),
];
