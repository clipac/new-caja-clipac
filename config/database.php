<?php
return [
    'default' => 'local',
    'connections' => [
        'local' => [
            'driver' => env('DBL_CONNECTION'),
            'host' => env('DBL_HOST'),
            'database' => env('DBL_DATABASE'),
            'username' => env('DBL_USERNAME'),
            'password' => env('DBL_PASSWORD'),
            'charset'   => 'utf8',
            'collation' => 'utf8_spanish_ci',
        ],
        'central' => [
            'driver' => env('DBC_CONNECTION'),
            'host' => env('DBC_HOST'),
            'database' => env('DBC_DATABASE'),
            'username' => env('DBC_USERNAME'),
            'password' => env('DBC_PASSWORD'),
            'charset'   => 'utf8',
            'collation' => 'utf8_spanish_ci',
        ],
    ],
    'migrations' => 'migrations',
];
