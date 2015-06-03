<?php

/*
|--------------------------------------------------------------------------
| Doctrine2l5 :: Doctrine Configuration
|--------------------------------------------------------------------------
|
| See: https://github.com/jeanbelhache/doctrine2-l5
|
| NB: Database configuration taken from Laravel5's own config/database.php
| settings. Implemented databases are: MySQL, Postgres and SQLite.
*/

return [

    // connection parameters are set in Laravael's own database config file.

    // Paths for models, proxies, repositories, etc.
    'paths' => [
        'entities'     => app()->databasePath() . '/Entities',
        'proxies'      => app()->databasePath() . '/Proxies',
        'repositories' => app()->databasePath() . '/Repositories'
        'yml'          => app()->databasePath() . '/Yml'
    ],

    // set to true to have Doctrine2 generate proxies on the fly. Not recommended in a production system.
    'autogen_proxies'       => FALSE,

    // Namespaces for entities, proxies and repositories.
    'namespaces' => [
        'entities'          => 'Entities',
        'proxies'           => 'Proxies',
        'repositories'      => 'Repositories'
    ],

    // Doctrine2 includes an implementation of Doctrine\DBAL\Logging\SQLLogger which
    // just calls the Laravel Log facade. If you wish to log your SQL queries (and execution
    // time), just set enabled in the following to true.
    'sqllogger' => [
        'enabled' => env('APP_DEBUG'),
        'level'   => 'debug'   // one of debug, info, notice, warning, error, critical, alert
    ],

    // use Doctrine2 with Laravel's authentication menchanism
    'auth' => [
        'enabled' => FALSE,
        'entity'  => '\Entities\User'   // the Doctrine2 entity representing the user
    ]
];
