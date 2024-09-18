<?php

namespace PlusInfoLab\ArtisanBatch;

use Illuminate\Support\ServiceProvider;

class ArtisanBatchServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/batch.php', 'batch');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/batch.php' => config_path('batch.php'),
        ], 'config');

        $this->commands([
            Commands\RunBatchCommand::class,
            Commands\ListBatchCommand::class,
        ]);
    }
}
