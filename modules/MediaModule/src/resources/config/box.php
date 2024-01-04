<?php

return [
    'modules' => [

    ],
    'routes' => [
        'files' => ['api'],
        'prefix' => 'api/v1',
        'middleware' => ['api'],
    ],
    'views' => [
        'namespace' => 'media',
    ],
];
