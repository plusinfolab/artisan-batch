# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2024-09-18

### Added

- Initial release of Artisan Batch
- `batch:run` command to execute predefined recipes
- `batch:list` command to view available recipes
- 10 built-in recipes:
  - `deploy` - Production deployment with migration and caching
  - `local-setup` - Local development setup with migration and seeding
  - `local-reset` - Complete database reset with fresh migration
  - `cache-clear` - Clear all application caches
  - `optimize` - Optimize for production environment
  - `maintenance-on` - Enable maintenance mode
  - `maintenance-off` - Disable maintenance mode and clear caches
  - `backup-database` - Database backup (requires spatie/laravel-backup)
  - `test` - Run tests with coverage report
  - `health-check` - Check application health status
- Dry-run mode (`--dry`) to preview commands without execution
- JSON output (`--json`) for CI/CD integration
- Continue on failure option (`--no-stop`)
- Detailed output option (`--show-output`)
- Command execution timing and statistics
- Configurable stop-on-failure behavior
- Environment-specific settings (production/local)
- Protection for destructive commands in production
- Detailed logging to `storage/logs/artisan-batch.log`
- Support for Laravel 10.x, 11.x, and 12.x
- Support for PHP 8.1+

[1.0.0]: https://github.com/plusinfolab/artisan-batch/releases/tag/v1.0.0
