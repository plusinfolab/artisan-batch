<?php

namespace PlusInfoLab\ArtisanBatch\Commands;

use Illuminate\Console\Command;

class ListBatchCommand extends Command
{
    protected $signature = 'batch:list
                            {--json : Output in JSON format}
                            {--detailed : Show detailed command information}';

    protected $description = 'List all available batch recipes';

    public function handle()
    {
        $recipes = config('batch.recipes', []);
        $stopOnFailure = config('batch.stop_on_failure', true);

        if (empty($recipes)) {
            $this->error('No recipes found in batch configuration.');
            return 1;
        }

        if ($this->option('json')) {
            $this->outputJson($recipes, $stopOnFailure);
        } else {
            $this->outputTable($recipes, $stopOnFailure);
        }

        return 0;
    }

    private function outputTable(array $recipes, bool $stopOnFailure): void
    {
        $this->info("📋 Available Batch Recipes");
        $this->line(str_repeat('=', 50));
        $this->comment("Stop on failure: " . ($stopOnFailure ? "YES" : "NO"));
        $this->line(str_repeat('-', 50));

        $tableData = [];
        foreach ($recipes as $name => $commands) {
            $tableData[] = [
                'recipe' => $name,
                'commands' => count($commands),
                'description' => $this->getDescriptionForRecipe($name, $commands)
            ];
        }

        $this->table(['Recipe', 'Commands', 'Description'], $tableData);

        if ($this->option('detailed')) {
            $this->line(str_repeat('=', 50));
            $this->info("\n📝 Detailed Recipe Information:\n");

            foreach ($recipes as $name => $commands) {
                $this->comment("{$name} (" . count($commands) . " commands):");
                foreach ($commands as $index => $command) {
                    $this->line("  " . ($index + 1) . ". {$command}");
                }
                $this->line('');
            }
        }

        $this->info("Usage:");
        $this->comment("  php artisan batch:run <recipe>          # Run a recipe");
        $this->comment("  php artisan batch:run <recipe> --dry    # Dry run");
        $this->comment("  php artisan batch:run <recipe> --json   # JSON output");
    }

    private function outputJson(array $recipes, bool $stopOnFailure): void
    {
        $data = [
            'recipes' => [],
            'configuration' => [
                'stop_on_failure' => $stopOnFailure
            ]
        ];

        foreach ($recipes as $name => $commands) {
            $data['recipes'][$name] = [
                'commands' => $commands,
                'count' => count($commands),
                'description' => $this->getDescriptionForRecipe($name, $commands)
            ];
        }

        $this->line(json_encode($data, JSON_PRETTY_PRINT));
    }

    private function getDescriptionForRecipe(string $name, array $commands): string
    {
        $descriptions = [
            'deploy' => 'Standard deployment commands',
            'local-reset' => 'Reset local database with fresh migration',
            'cache-clear' => 'Clear all application caches',
            'backup' => 'Create application backup',
            'maintenance' => 'Maintenance mode commands',
        ];

        return $descriptions[$name] ?? 'Custom recipe';
    }
}