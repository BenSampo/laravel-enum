<?php

namespace BenSampo\Enum\Tests;

use Orchestra\Testbench\TestCase;
use BenSampo\Enum\Tests\Enums\UserTypeWithLocale;

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

    public function test_enum_get_description_with_localization()
    {
        $this->app->setLocale('en');
        $this->assertEquals('Super administrator', UserTypeWithLocale::getDescription(UserTypeWithLocale::SuperAdministrator));

        $this->app->setLocale('es');
        $this->assertEquals('Súper administrador', UserTypeWithLocale::getDescription(UserTypeWithLocale::SuperAdministrator));
    }

    public function test_enum_get_description_for_missing_localization_key()
    {
        $this->app->setLocale('en');
        $this->assertEquals('Moderator', UserTypeWithLocale::getDescription(UserTypeWithLocale::Moderator));

        $this->app->setLocale('es');
        $this->assertEquals('Moderator', UserTypeWithLocale::getDescription(UserTypeWithLocale::Moderator));
    }
}
