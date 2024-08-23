<?php

declare(strict_types=1);

namespace Dex\Pest\Plugin\Laravel\Tester\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase as Orchestra;
use Workbench\App\Providers\WorkbenchServiceProvider;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Workbench\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        Factory::guessModelNamesUsing(
            fn ($factory) => 'Workbench\\App\\Models\\'.Str::replaceLast('Factory', '', class_basename($factory))
        );

        $this->loadLaravelMigrations();
    }

    protected function getPackageProviders($app): array
    {
        return [
            WorkbenchServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../workbench/database/migrations');
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
    }
}
