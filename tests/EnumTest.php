<?php

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Enum;
use PHPUnit\Framework\TestCase;
use BenSampo\Enum\Tests\Enums\UserType;
use BenSampo\Enum\Tests\Enums\StringValues;
use BenSampo\Enum\Tests\Enums\MixedKeyFormats;

class EnumTest extends TestCase
{
    public function test_enum_values()
    {
        $this->assertEquals(0, UserType::Administrator);
        $this->assertEquals(3, UserType::SuperAdministrator);
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
        $this->assertEquals(UserType::Administrator, $enum->value);

        $enum = UserType::coerce(UserType::Administrator()->key);
        $this->assertEquals(UserType::Administrator, $enum->value);

        $enum = UserType::coerce(-1);
        $this->assertEquals(null, $enum);

        $enum = UserType::coerce(null);
        $this->assertEquals(null, $enum);
    }

    public function test_enum_get_values()
    {
        $values = UserType::getValues();
        $expectedValues = [0, 1, 2, 3];

        $this->assertEquals($expectedValues, $values);
    }

    public function test_enum_get_key()
    {
        $this->assertEquals('Moderator', UserType::getKey(1));
        $this->assertEquals('SuperAdministrator', UserType::getKey(3));
    }

    public function test_enum_get_key_using_string_value()
    {
        $this->assertEquals('Administrator', StringValues::getKey('administrator'));
    }

    public function test_enum_get_value()
    {
        $this->assertEquals(1, UserType::getValue('Moderator'));
        $this->assertEquals(3, UserType::getValue('SuperAdministrator'));
    }

    public function test_enum_get_value_using_string_key()
    {
        $this->assertEquals('administrator', StringValues::getValue('Administrator'));
    }

    public function test_enum_get_description()
    {
        $this->assertEquals('Normal', MixedKeyFormats::getDescription(MixedKeyFormats::Normal));
        $this->assertEquals('Multi word key name', MixedKeyFormats::getDescription(MixedKeyFormats::MultiWordKeyName));
        $this->assertEquals('Uppercase', MixedKeyFormats::getDescription(MixedKeyFormats::UPPERCASE));
        $this->assertEquals('Uppercase snake case', MixedKeyFormats::getDescription(MixedKeyFormats::UPPERCASE_SNAKE_CASE));
        $this->assertEquals('Lowercase snake case', MixedKeyFormats::getDescription(MixedKeyFormats::lowercase_snake_case));
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
        $this->assertEquals('1', json_encode(UserType::Moderator()));
    }
}
