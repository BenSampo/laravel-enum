<?php

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Enum;
use PHPUnit\Framework\TestCase;
use BenSampo\Enum\Tests\Enums\UserType;
use BenSampo\Enum\Tests\Enums\SuperPowers;
use BenSampo\Enum\Tests\Enums\StringValues;
use BenSampo\Enum\Tests\Enums\MixedKeyFormats;

class EnumTest extends TestCase
{
    public function test_enum_values()
    {
        $this->assertSame(0, UserType::Administrator);
        $this->assertSame(3, UserType::SuperAdministrator);
    }

    public function test_enum_get_keys()
    {
        $keys = UserType::getKeys();
        $expectedKeys = ['Administrator', 'Moderator', 'Subscriber', 'SuperAdministrator'];

        $this->assertEquals($expectedKeys, $keys);
    }

    public function test_enum_coerce()
    {
        $enum = UserType::coerce(UserType::Administrator()->value);
        $this->assertInstanceOf(UserType::class, $enum);
        $this->assertSame(UserType::Administrator, $enum->value);

        $enum = UserType::coerce(UserType::Administrator()->key);
        $this->assertInstanceOf(UserType::class, $enum);
        $this->assertSame(UserType::Administrator, $enum->value);

        $enum = UserType::coerce(-1);
        $this->assertNull($enum);

        $enum = UserType::coerce(null);
        $this->assertNull($enum);

        $enum = UserType::coerce(UserType::Administrator());
        $this->assertSame(UserType::Administrator, $enum->value);

        $enum = SuperPowers::coerce(SuperPowers::LaserVision);
        $this->assertInstanceOf(SuperPowers::class, $enum);

        $enum = SuperPowers::coerce(SuperPowers::LaserVision()->key);
        $this->assertInstanceOf(SuperPowers::class, $enum);

        $enum = SuperPowers::coerce(3);
        $this->assertInstanceOf(SuperPowers::class, $enum);

        $enum = SuperPowers::coerce(SuperPowers::flags([SuperPowers::Flight, SuperPowers::LaserVision]));
        $this->assertInstanceOf(SuperPowers::class, $enum);

        $enum = SuperPowers::coerce('Test');
        $this->assertNull($enum);
    }

    public function test_enum_get_values()
    {
        $values = UserType::getValues();
        $expectedValues = [0, 1, 2, 3];

        $this->assertEquals($expectedValues, $values);
    }

    public function test_enum_get_key()
    {
        $this->assertSame('Moderator', UserType::getKey(1));
        $this->assertSame('SuperAdministrator', UserType::getKey(3));
    }

    public function test_enum_get_key_using_string_value()
    {
        $this->assertSame('Administrator', StringValues::getKey('administrator'));
    }

    public function test_enum_get_value()
    {
        $this->assertSame(1, UserType::getValue('Moderator'));
        $this->assertSame(3, UserType::getValue('SuperAdministrator'));
    }

    public function test_enum_get_value_using_string_key()
    {
        $this->assertSame('administrator', StringValues::getValue('Administrator'));
    }

    public function test_enum_get_description()
    {
        $this->assertSame('Normal', MixedKeyFormats::getDescription(MixedKeyFormats::Normal));
        $this->assertSame('Multi word key name', MixedKeyFormats::getDescription(MixedKeyFormats::MultiWordKeyName));
        $this->assertSame('Uppercase', MixedKeyFormats::getDescription(MixedKeyFormats::UPPERCASE));
        $this->assertSame('Uppercase snake case', MixedKeyFormats::getDescription(MixedKeyFormats::UPPERCASE_SNAKE_CASE));
        $this->assertSame('Lowercase snake case', MixedKeyFormats::getDescription(MixedKeyFormats::lowercase_snake_case));
        $this->assertSame('Uppercase snake case numeric suffix 2', MixedKeyFormats::getDescription(MixedKeyFormats::UPPERCASE_SNAKE_CASE_NUMERIC_SUFFIX_2));
        $this->assertSame('Lowercase snake case numeric suffix 2', MixedKeyFormats::getDescription(MixedKeyFormats::lowercase_snake_case_numeric_suffix_2));
    }

    public function test_enum_get_random_key()
    {
        $this->assertContains(UserType::getRandomKey(), UserType::getKeys());
    }

    public function test_enum_get_random_value()
    {
        $this->assertContains(UserType::getRandomValue(), UserType::getValues());
    }

    public function test_enum_to_array()
    {
        $array = UserType::asArray();
        $expectedArray = [
            'Administrator' => 0,
            'Moderator' => 1,
            'Subscriber' => 2,
            'SuperAdministrator' => 3,
        ];

        $this->assertEquals($expectedArray, $array);
    }

    public function test_enum_as_select_array()
    {
        $array = UserType::asSelectArray();
        $expectedArray = [
            0 => 'Administrator',
            1 => 'Moderator',
            2 => 'Subscriber',
            3 => 'Super administrator',
        ];

        $this->assertEquals($expectedArray, $array);
    }

    public function test_enum_as_select_array_with_string_values()
    {
        $array = StringValues::asSelectArray();
        $expectedArray = [
            'administrator' => 'Administrator',
            'moderator' => 'Moderator',
        ];

        $this->assertEquals($expectedArray, $array);
    }

    public function test_enum_is_macroable_with_static_methods()
    {
        Enum::macro('asFlippedArray', function () {
            return array_flip(self::asArray());
        });

        $this->assertTrue(UserType::hasMacro('asFlippedArray'));
        $this->assertEquals(UserType::asFlippedArray(), array_flip(UserType::asArray()));
    }

    public function test_enum_is_macroable_with_instance_methods()
    {
        Enum::macro('macroGetValue', function () {
            return $this->value;
        });

        $this->assertTrue(UserType::hasMacro('macroGetValue'));

        $user = new UserType(UserType::Administrator);
        $this->assertSame(UserType::Administrator, $user->macroGetValue());
    }

    public function test_enum_get_instances()
    {
        /** @var StringValues $administrator */
        /** @var StringValues $moderator */
        [
            'Administrator' => $administrator,
            'Moderator' => $moderator
        ] = StringValues::getInstances();

        $this->assertTrue(
            $administrator->is(StringValues::Administrator)
        );

        $this->assertTrue(
            $moderator->is(StringValues::Moderator)
        );
    }

    public function test_enum_can_be_cast_to_string()
    {
        $enumWithZeroIntegerValue = new UserType(UserType::Administrator);
        $enumWithPositiveIntegerValue = new UserType(UserType::SuperAdministrator);
        $enumWithStringValue = new StringValues(StringValues::Moderator);

        // Numbers should be cast to strings
        $this->assertSame('0', (string) $enumWithZeroIntegerValue);
        $this->assertSame('3', (string) $enumWithPositiveIntegerValue);

        // Strings should just be returned
        $this->assertSame(StringValues::Moderator, (string) $enumWithStringValue);
    }

    public function test_enum_can_be_json_encoded()
    {
        $this->assertSame('1', json_encode(UserType::Moderator()));
    }
}
