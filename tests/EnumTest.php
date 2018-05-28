<?php

namespace BenSampo\Enum\Tests;

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
        $expectedKeys = ['Administrator', 'Moderator', 'Subscriber', 'SuperAdministrator', 'StringKey'];

        $this->assertEquals($expectedKeys, $keys);
    }

    public function testEnumGetValues()
    {
        $values = UserType::getValues();
        $expectedValues = [0, 1, 2, 3, 'StringValue'];

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
        $this->assertEquals('Super admin', UserType::getDescription(3));
        $this->assertEquals('StringKey', UserType::getDescription('StringValue'));
    }
}
