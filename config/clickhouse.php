<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ClickHouse Connection Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for ClickHouse database connection
    |
    */

    'host' => env('CLICKHOUSE_HOST', 'localhost'),
    'port' => env('CLICKHOUSE_PORT', '8123'),
    'username' => env('CLICKHOUSE_USERNAME', 'default'),
    'password' => env('CLICKHOUSE_PASSWORD', 'clickhouse'),
    'database' => env('CLICKHOUSE_DATABASE', 'weather'),
    
    /*
    |--------------------------------------------------------------------------
    | Connection Settings
    |--------------------------------------------------------------------------
    */
    
    'settings' => [
        'max_execution_time' => 60,
        'max_block_size' => 10000,
        'readonly' => 0,
    ],
];

