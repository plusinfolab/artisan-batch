# Artisan Batch

A Laravel package for running multiple Artisan commands in sequence using predefined recipes. Perfect for deployment scripts, local development setup, and maintenance operations.

## Features

- =€ Execute multiple Artisan commands with a single command
- =Ë Predefined recipes for common operations (deploy, cache clearing, testing, etc.)
- ™ Customizable configuration with environment-specific settings
- = Dry-run mode to preview commands before execution
- =Ê JSON output for CI/CD integration
- =á Error handling with configurable stop-on-failure behavior
- =Ý Detailed logging and execution statistics
- ñ Command execution timing
- = Protection for destructive commands in production

## Installation

1. Install the package via Composer:

```bash
composer require plusinfolab/artisan-batch
```

2. Publish the configuration file:

```bash
php artisan vendor:publish --provider="PlusInfoLab\ArtisanBatch\ArtisanBatchServiceProvider" --tag="config"
```

## Usage

### List Available Recipes

```bash
php artisan batch:list
```

For detailed view:

```bash
php artisan batch:list --detailed
```

For JSON output:

```bash
php artisan batch:list --json
```

### Run a Recipe

```bash
php artisan batch:run <recipe-name>
```

### Examples

#### Deploy to Production
```bash
php artisan batch:run deploy
```

#### Local Development Setup
```bash
php artisan batch:run local-setup
```

#### Clear All Caches
```bash
php artisan batch:run cache-clear
```

#### Database Reset (Local)
```bash
php artisan batch:run local-reset
```

### Advanced Usage

#### Dry Run (Preview Commands)
```bash
php artisan batch:run deploy --dry
```

#### Continue on Failure
```bash
php artisan batch:run deploy --no-stop
```

#### Verbose Output
```bash
php artisan batch:run deploy --verbose
```

#### JSON Output (for CI/CD)
```bash
php artisan batch:run deploy --json
```

## Configuration

### Publish Configuration

After publishing, you'll find the configuration at `config/batch.php`.

### Define Custom Recipes

```php
'recipes' => [
    'my-custom-recipe' => [
        'cache:clear',
        'migrate --force',
        'queue:restart',
    ],
],
```

### Global Settings

- `stop_on_failure`: Stop execution when a command fails (default: true)
- `command_timeout`: Timeout for each command in seconds (default: 300)
- `enable_logging`: Enable detailed logging (default: true)
- `log_file`: Log file path relative to storage/logs (default: 'artisan-batch.log')

### Environment-Specific Settings

```php
'environments' => [
    'production' => [
        'stop_on_failure' => true,
        'confirm_destructive' => true,
        'default_recipes' => ['deploy', 'optimize']
    ],
],
```

## Built-in Recipes

| Recipe | Description | Environment |
|--------|-------------|-------------|
| `deploy` | Production deployment with migration and caching | Production |
| `local-setup` | Local development setup with migration and seeding | Local |
| `local-reset` | Complete database reset with fresh migration | Local |
| `cache-clear` | Clear all application caches | Any |
| `optimize` | Optimize for production environment | Production |
| `maintenance-on` | Enable maintenance mode | Production |
| `maintenance-off` | Disable maintenance mode and clear caches | Production |
| `test` | Run tests with coverage report | Local |
| `health-check` | Check application health status | Any |

## Output Examples

### Standard Output
```
=€ Running batch 'deploy'...
Configuration: EXECUTE | Stop on failure: YES
============================================================
1. cache:clear
Output: Application cache cleared successfully.
Time: 15.23ms | Exit: 0
----------------------------------------
2. config:clear
Output: Configuration cache cleared successfully.
Time: 8.45ms | Exit: 0
----------------------------------------
...
============================================================
 Batch 'deploy' completed successfully!
Summary: 8 successful, 0 failed, 0 skipped
```

### JSON Output
```json
{
  "recipe": "deploy",
  "dry_run": false,
  "start_time": "2024-01-15T10:30:00.000Z",
  "commands": [
    {
      "index": 1,
      "command": "cache:clear",
      "status": "success",
      "output": "Application cache cleared successfully.",
      "exit_code": 0,
      "execution_time": 15.23
    }
  ],
  "summary": {
    "total": 8,
    "successful": 8,
    "failed": 0,
    "skipped": 0
  },
  "end_time": "2024-01-15T10:32:15.000Z",
  "completed": true
}
```

## CI/CD Integration

### GitHub Actions Example
```yaml
- name: Deploy to Production
  run: |
    php artisan batch:run deploy --json > deploy_output.json

- name: Verify Deployment
  run: |
    jq -e '.completed == true and .summary.failed == 0' deploy_output.json
```

### Laravel Forge Integration
```bash
# Deploy script for Forge
php artisan down
php artisan batch:run deploy
php artisan up
```

## Security Considerations

- Destructive commands are flagged and may require confirmation in production
- Recipes with `migrate --force` are automatically protected in production environments
- All command execution is logged for audit purposes
- JSON output can be parsed for automated monitoring

## Best Practices

1. **Always test recipes locally**: Use `--dry` to preview commands before running in production
2. **Use environment-specific recipes**: Leverage the environment configuration for different deployment strategies
3. **Monitor execution logs**: Check `storage/logs/artisan-batch.log` for detailed execution history
4. **Implement monitoring**: Use JSON output to integrate with monitoring systems
5. **Version control recipes**: Keep your `config/batch.php` in version control

## Troubleshooting

### Command Not Found
```bash
php artisan batch:list
```
If you get "command not found", ensure the service provider is properly registered.

### Permission Issues
Make sure your Laravel application has write permissions to the `storage/logs` directory for logging.

### Recipe Not Found
Check that the recipe name exists in your `config/batch.php` file and run `php artisan batch:list` to see available recipes.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).