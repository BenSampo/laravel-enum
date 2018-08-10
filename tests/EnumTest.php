<?php

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Enum;
use PHPUnit\Framework\TestCase;

class EnumTest extends TestCase
{
    public function testEnumValues()
    {
        $this->assertEquals(0, UserType::Administrator);
        $this->assertEquals(3, UserType::SuperAdministrator);
    }

    public function testEnumGetKeys()
    {
        $keys = UserType::getKeys();
        $expectedKeys = ['Administrator', 'Moderator', 'Subscriber', 'SuperAdministrator', 'FourWordKeyName', 'StringKey'];

        $this->assertEquals($expectedKeys, $keys);
    }

    public function testEnumGetValues()
    {
        $values = UserType::getValues();
        $expectedValues = [0, 1, 2, 3, 4, 'StringValue'];

        $this->assertEquals($expectedValues, $values);
    }

    public function testEnumGetKey()
    {
        $this->assertEquals('Moderator', UserType::getKey(1));
        $this->assertEquals('SuperAdministrator', UserType::getKey(3));
        $this->assertEquals('StringKey', UserType::getKey('StringValue'));
    }

    public function testEnumGetValue()
    {
        $this->assertEquals(1, UserType::getValue('Moderator'));
        $this->assertEquals(3, UserType::getValue('SuperAdministrator'));
        $this->assertEquals('StringValue', UserType::getValue('StringKey'));
    }

    public function testEnumGetDescription()
    {
        $this->assertEquals('Moderator', UserType::getDescription(1));
        $this->assertEquals('Super administrator', UserType::getDescription(3));
        $this->assertEquals('Four word key name', UserType::getDescription(4));
        $this->assertEquals('String key', UserType::getDescription('StringValue'));
    }

    public function testEnumGetRandomKey()
    {
        $this->assertContains(UserType::getRandomKey(), UserType::getKeys());
    }

    public function testEnumGetRandomValue()
    {
        $this->assertContains(UserType::getRandomValue(), UserType::getValues());
    }

    public function testEnumToArray()
    {
        $array = UserType::toArray();
        $expectedArray = [
            'Administrator' => 0,
            'Moderator' => 1,
            'Subscriber' => 2,
            'SuperAdministrator' => 3,
            'FourWordKeyName' => 4,
            'StringKey' => 'StringValue',
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
