<?php

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Rules\Enum;
use BenSampo\Enum\Rules\EnumKey;
use BenSampo\Enum\Tests\Enums\UserType;
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

    public function test_can_localize_validation_error_message_using_class_rule()
    {
        $validator = $this->app['validator'];

        $this->assertSame(
            'The value you have provided is not a valid enum instance.',
            $validator->make(['input' => 'test'], ['input' => new Enum(UserType::class)])->errors()->first()
        );
        $this->assertSame(
            'Wrong key.',
            $validator->make(['input' => 'test'], ['input' => new EnumKey(UserType::class)])->errors()->first()
        );

        $this->app->setLocale('es');

        $this->assertSame(
            'The value you have provided is not a valid enum instance.', // No Spanish translations out of the box
            $validator->make(['input' => 'test'], ['input' => new Enum(UserType::class)])->errors()->first()
        );
        $this->assertSame(
            'Llave incorrecta.',
            $validator->make(['input' => 'test'], ['input' => new EnumKey(UserType::class)])->errors()->first()
        );
    }

    public function test_can_localize_validation_error_message_using_string_rule()
    {
        $validator = $this->app['validator'];

        $this->assertSame(
            'The value you have provided is not a valid enum instance.',
            $validator->make(['input' => 'test'], ['input' => 'enum:' . UserType::class])->errors()->first()
        );
        $this->assertSame(
            'Wrong key.',
            $validator->make(['input' => 'test'], ['input' => 'enum_key:' . UserType::class])->errors()->first()
        );

        $this->app->setLocale('es');

        $this->assertSame(
            'The value you have provided is not a valid enum instance.', // No Spanish translations out of the box
            $validator->make(['input' => 'test'], ['input' => 'enum:' . UserType::class])->errors()->first()
        );
        $this->assertSame(
            'Llave incorrecta.',
            $validator->make(['input' => 'test'], ['input' => 'enum_key:' . UserType::class])->errors()->first()
        );
    }
}
