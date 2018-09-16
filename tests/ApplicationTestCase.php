<?php

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\EnumServiceProvider;

class ApplicationTestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            EnumServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['path.lang'] = __DIR__ . '/lang';
    }
}