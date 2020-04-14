<?php

declare(strict_types=1);

/*
 * This file is part of Laravel Awx.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
    */

    'default' => 'main',

    /*
    |--------------------------------------------------------------------------
    | Awx Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application. Example
    | configuration has been included, but you may add as many connections as
    | you would like. Both guzzle and buzz drivers are supported.
    |
    */

    'connections' => [

        'main' => [
            'driver'  => 'guzzle',
        ],

        'other' => [
            'driver'  => 'buzz',
        ],

    ],
    
    'oauth' => [

        'username' => env('AWX_USERNAME'),
        'password' => env('AWX_PASSWORD'),
        'clientId' => env('AWX_CLIENT_ID'),
        'clientSecret' => env('AWX_CLIENT_SECRET'),

    ],
    
    'api' => [
        'url' => [
            'base' => env('AWX_API_URL'), //https://x.x.x.x/api
        ],
        'ssl' => [
            'verify' => env('AWX_SSL_VERIFY'), // Set to false for development, true or blank when public ssl certificates are set up 
        ],
    ],

];
