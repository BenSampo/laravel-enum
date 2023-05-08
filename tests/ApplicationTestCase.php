<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use Orchestra\Testbench\TestCase;
use BenSampo\Enum\EnumServiceProvider;

class ApplicationTestCase extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    protected function getPackageProviders($app): array
    {
        return [
            EnumServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['path.lang'] = __DIR__ . '/lang';

        $database = 'testbench';
        $app['config']->set('database.default', $database);
        $app['config']->set("database.connections.{$database}", [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
