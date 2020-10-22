<?php

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Tests\Enums\UserTypeLocalized;

class EnumLocalizationTest extends ApplicationTestCase
{
    public function test_enum_get_description_with_localization()
    {
        $this->app->setLocale('en');
        $this->assertSame('Super administrator', UserTypeLocalized::getDescription(UserTypeLocalized::SuperAdministrator));

        $this->app->setLocale('es');
        $this->assertSame('Súper administrador', UserTypeLocalized::getDescription(UserTypeLocalized::SuperAdministrator));
    }

    public function test_enum_get_description_for_missing_localization_key()
    {
        $this->app->setLocale('en');
        $this->assertSame('Moderator', UserTypeLocalized::getDescription(UserTypeLocalized::Moderator));

        $this->app->setLocale('es');
        $this->assertSame('Moderator', UserTypeLocalized::getDescription(UserTypeLocalized::Moderator));
    }
}
