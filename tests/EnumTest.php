<?php

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Enum;
use PHPUnit\Framework\TestCase;

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
        $expectedKeys = ['Administrator', 'Moderator', 'Subscriber', 'SuperAdministrator', 'FourWordKeyName', 'UPPERCASE', 'StringKey'];

        $this->assertEquals($expectedKeys, $keys);
    }

    public function test_enum_get_values()
    {
        $values = UserType::getValues();
        $expectedValues = [0, 1, 2, 3, 4, 5, 'StringValue'];

        $this->assertEquals($expectedValues, $values);
    }

    public function test_enum_get_key()
    {
        $this->assertEquals('Moderator', UserType::getKey(1));
        $this->assertEquals('SuperAdministrator', UserType::getKey(3));
        $this->assertEquals('StringKey', UserType::getKey('StringValue'));
    }

    public function test_enum_get_value()
    {
        $this->assertEquals(1, UserType::getValue('Moderator'));
        $this->assertEquals(3, UserType::getValue('SuperAdministrator'));
        $this->assertEquals('StringValue', UserType::getValue('StringKey'));
    }

    public function test_enum_get_description()
    {
        $this->assertEquals('Moderator', UserType::getDescription(1));
        $this->assertEquals('Super administrator', UserType::getDescription(3));
        $this->assertEquals('Four word key name', UserType::getDescription(4));
        $this->assertEquals('String key', UserType::getDescription('StringValue'));
        $this->assertEquals('Uppercase', UserType::getDescription(5));
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
        $array = UserType::toArray();
        $expectedArray = [
            'Administrator' => 0,
            'Moderator' => 1,
            'Subscriber' => 2,
            'SuperAdministrator' => 3,
            'FourWordKeyName' => 4,
            'UPPERCASE' => 5,
            'StringKey' => 'StringValue',
        ];

        $this->assertEquals($expectedArray, $array);
    }

    public function test_enum_to_select_array()
    {
        $array = UserType::toSelectArray();
        $expectedArray = [
            0 => 'Administrator',
            1 => 'Moderator',
            2 => 'Subscriber',
            3 => 'Super administrator',
            4 => 'Four word key name',
            5 => 'Uppercase',
            'StringValue' => 'String key',
        ];

        $this->assertEquals($expectedArray, $array);
    }

    public function test_enum_is_macroable()
    {
        Enum::macro('toFlippedArray', function() {
            return array_flip(self::toArray());
        });

        $this->assertTrue(UserType::hasMacro('toFlippedArray'));
        $this->assertEquals(UserType::toFlippedArray(), array_flip(UserType::toArray()));
    }
}
