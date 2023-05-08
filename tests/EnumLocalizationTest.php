<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Rules\Enum;
use BenSampo\Enum\Rules\EnumKey;
use BenSampo\Enum\Rules\EnumValue;
use BenSampo\Enum\Tests\Enums\UserType;
use BenSampo\Enum\Tests\Enums\UserTypeLocalized;

final class EnumLocalizationTest extends ApplicationTestCase
{
    public function test_enum_get_description_with_localization(): void
    {
        $this->app->setLocale('en');
        $this->assertSame('Super administrator', UserTypeLocalized::getDescription(UserTypeLocalized::SuperAdministrator));

        $this->app->setLocale('es');
        $this->assertSame('Súper administrador', UserTypeLocalized::getDescription(UserTypeLocalized::SuperAdministrator));
    }

    public function test_enum_get_description_for_missing_localization_key(): void
    {
        $this->app->setLocale('en');
        $this->assertSame('Moderator', UserTypeLocalized::getDescription(UserTypeLocalized::Moderator));

        $this->app->setLocale('es');
        $this->assertSame('Moderator', UserTypeLocalized::getDescription(UserTypeLocalized::Moderator));
    }

    public function test_can_localize_validation_error_message_using_class_rule(): void
    {
        $validator = $this->app['validator'];

        $this->assertSame(
            'The value you have entered is invalid.',
            $validator->make(['input' => 'test'], ['input' => new EnumValue(UserType::class)])->errors()->first()
        );
        $this->assertSame(
            'Wrong key.',
            $validator->make(['input' => 'test'], ['input' => new EnumKey(UserType::class)])->errors()->first()
        );

        $this->app->setLocale('es');

        $this->assertSame(
            'The value you have entered is invalid.', // No Spanish translations out of the box
            $validator->make(['input' => 'test'], ['input' => new EnumValue(UserType::class)])->errors()->first()
        );
        $this->assertSame(
            'Llave incorrecta.',
            $validator->make(['input' => 'test'], ['input' => new EnumKey(UserType::class)])->errors()->first()
        );
    }

    public function test_can_localize_validation_error_message_using_string_rule(): void
    {
        $validator = $this->app['validator'];

        $this->assertSame(
            'The value you have entered is invalid.',
            $validator->make(['input' => 'test'], ['input' => 'enum_value:BenSampo\Enum\Tests\Enums\UserType'])->errors()->first()
        );
        $this->assertSame(
            'Wrong key.',
            $validator->make(['input' => 'test'], ['input' => 'enum_key:BenSampo\Enum\Tests\Enums\UserType'])->errors()->first()
        );

        $this->app->setLocale('es');

        $this->assertSame(
            'The value you have entered is invalid.', // No Spanish translations out of the box
            $validator->make(['input' => 'test'], ['input' => 'enum_value:BenSampo\Enum\Tests\Enums\UserType'])->errors()->first()
        );
        $this->assertSame(
            'Llave incorrecta.',
            $validator->make(['input' => 'test'], ['input' => 'enum_key:BenSampo\Enum\Tests\Enums\UserType'])->errors()->first()
        );
    }
}
