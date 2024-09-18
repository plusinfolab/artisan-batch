<?php

namespace PlusInfoLab\ArtisanBatch\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RunBatchCommand extends Command
{
    protected $signature = 'batch:run
                            {recipe? : The name of the recipe to run}
                            {--dry : Show commands without executing them}
                            {--no-stop : Continue execution even if a command fails}
                            {--show-output : Show detailed output for each command}
                            {--json : Output results in JSON format}';

    protected $description = 'Run a predefined batch of artisan commands';

    public function handle()
    {
        $recipe = $this->argument('recipe');
        $recipes = config('batch.recipes', []);

        if (!$recipe) {
            if ($this->option('json')) {
                $this->line(json_encode(['error' => 'No recipe specified'], JSON_PRETTY_PRINT));
            } else {
                $this->error('No recipe specified.');
                $this->info('Available recipes: ' . implode(', ', array_keys($recipes)));
                $this->info('Use "php artisan batch:list" to see all available recipes.');
            }
            return 1;
        }

        if (!isset($recipes[$recipe])) {
            $message = "Recipe '{$recipe}' not found.";
            if ($this->option('json')) {
                $this->line(json_encode(['error' => $message], JSON_PRETTY_PRINT));
            } else {
                $this->error($message);
                $this->info('Available recipes: ' . implode(', ', array_keys($recipes)));
            }
            return 1;
        }

        $commands = $recipes[$recipe];
        $stopOnFailure = !$this->option('no-stop') && config('batch.stop_on_failure', true);
        $dryRun = $this->option('dry');

        $results = [
            'recipe' => $recipe,
            'dry_run' => $dryRun,
            'start_time' => now()->toISOString(),
            'commands' => [],
            'summary' => [
                'total' => count($commands),
                'successful' => 0,
                'failed' => 0,
                'skipped' => 0
            ]
        ];

        if (!$this->option('json')) {
            $this->info("🚀 Running batch '{$recipe}'...\n");
            $this->info("Configuration: " . ($dryRun ? "DRY RUN" : "EXECUTE") .
                " | Stop on failure: " . ($stopOnFailure ? "YES" : "NO"));
            $this->line(str_repeat('=', 60));
        }

        foreach ($commands as $index => $cmd) {
            $commandResult = [
                'index' => $index + 1,
                'command' => $cmd,
                'status' => 'pending',
                'output' => '',
                'exit_code' => null,
                'execution_time' => null
            ];

            if (!$this->option('json')) {
                $this->line(($index + 1) . ". {$cmd}");
            }

            if ($dryRun) {
                $commandResult['status'] = 'skipped';
                $commandResult['output'] = 'Dry run - command not executed';
                $results['summary']['skipped']++;
                $results['commands'][] = $commandResult;
                continue;
            }

            $startTime = microtime(true);
            $exit = Artisan::call($cmd);
            $output = Artisan::output();
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            $commandResult['exit_code'] = $exit;
            $commandResult['output'] = trim($output);
            $commandResult['execution_time'] = $executionTime;
            $commandResult['status'] = $exit === 0 ? 'success' : 'failed';

            if (!$this->option('json')) {
                if ($this->option('show-output') || $exit !== 0) {
                    $this->comment("Output: " . trim($output));
                }
                $this->comment("Time: {$executionTime}ms | Exit: {$exit}");
                $this->line(str_repeat('-', 40));
            }

            $results['commands'][] = $commandResult;

            if ($exit === 0) {
                $results['summary']['successful']++;
            } else {
                $results['summary']['failed']++;

                if ($stopOnFailure) {
                    $errorMsg = "❌ Command failed: {$cmd}. Stopping batch execution.";
                    if ($this->option('json')) {
                        $results['error'] = $errorMsg;
                    } else {
                        $this->error($errorMsg);
                    }
                    $results['end_time'] = now()->toISOString();
                    $results['completed'] = false;

                    $this->outputResults($results);
                    return $exit;
                }
            }
        }

        $results['end_time'] = now()->toISOString();
        $results['completed'] = true;

        if (!$this->option('json')) {
            $this->line(str_repeat('=', 60));
            $this->info("✅ Batch '{$recipe}' completed successfully!");
            $this->comment("Summary: {$results['summary']['successful']} successful, " .
                "{$results['summary']['failed']} failed, " .
                "{$results['summary']['skipped']} skipped");
        }

        $this->outputResults($results);
        return $results['summary']['failed'] > 0 ? 1 : 0;
    }

    private function outputResults(array $results): void
    {
        if ($this->option('json')) {
            $this->line(json_encode($results, JSON_PRETTY_PRINT));
        }
    }
}
