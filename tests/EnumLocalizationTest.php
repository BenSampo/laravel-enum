<?php

namespace BenSampo\Enum\Tests;

use Orchestra\Testbench\TestCase;

class EnumLocalizationTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['path.lang'] = __DIR__."/lang";
    }

    public function testEnumGetDescriptionWithLocalization()
    {
        $this->app->setLocale('en');
        $this->assertEquals('Super administrator', UserTypeWithLocale::getDescription(UserType::SuperAdministrator));

        $this->app->setLocale('es');
        $this->assertEquals('Súper administrador', UserTypeWithLocale::getDescription(UserType::SuperAdministrator));
    }
}
