<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Batch Recipes
    |--------------------------------------------------------------------------
    |
    | Define collections of Artisan commands that can be run together as a batch.
    | Each recipe should contain an array of commands to execute in order.
    |
    */

    'recipes' => [

        // Production deployment recipe
        'deploy' => [
            'cache:clear',
            'config:clear',
            'route:clear',
            'view:clear',
            'migrate --force',
            'config:cache',
            'route:cache',
            'db:seed --force --class=ProductionSeeder'
        ],

        // Local development setup
        'local-setup' => [
            'cache:clear',
            'config:clear',
            'migrate',
            'db:seed',
            'storage:link'
        ],

        // Complete database reset for local development
        'local-reset' => [
            'migrate:fresh --seed',
            'storage:link'
        ],

        // Cache clearing recipe
        'cache-clear' => [
            'cache:clear',
            'config:clear',
            'route:clear',
            'view:clear'
        ],

        // Optimize production environment
        'optimize' => [
            'config:cache',
            'route:cache',
            'view:cache'
        ],

        // Maintenance mode operations
        'maintenance-on' => [
            'down --message="Site undergoing maintenance" --retry=60'
        ],

        'maintenance-off' => [
            'up',
            'cache:clear',
            'config:clear'
        ],

        // Backup operations
        'backup-database' => [
            'db:backup --compress' // Requires spatie/laravel-backup package
        ],

        // Testing recipe
        'test' => [
            'config:clear',
            'migrate:fresh',
            'test --coverage',
        ],

        // Health check
        'health-check' => [
            'schedule:test',
            'queue:failed',
            'cache:status'
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration Options
    |--------------------------------------------------------------------------
    |
    | Global settings for batch execution behavior.
    |
    */

    // Stop execution when a command fails
    'stop_on_failure' => true,

    // Default timeout for each command (in seconds)
    'command_timeout' => 300,

    // Enable detailed logging
    'enable_logging' => true,

    // Log file path (relative to storage/logs)
    'log_file' => 'artisan-batch.log',

    // Environment-specific settings
    'environments' => [
        'production' => [
            'stop_on_failure' => true,
            'confirm_destructive' => true,
            'default_recipes' => ['deploy', 'optimize']
        ],
        'local' => [
            'stop_on_failure' => false,
            'confirm_destructive' => false,
            'default_recipes' => ['local-setup', 'test']
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Destructive Commands
    |--------------------------------------------------------------------------
    |
    | Commands that should require confirmation in production environments.
    |
    */
    'destructive_commands' => [
        'migrate:fresh',
        'migrate:reset',
        'migrate:rollback',
        'db:seed',
        'db:wipe',
        'down',
    ],

];
