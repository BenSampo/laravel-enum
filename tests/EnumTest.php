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
        $expectedKeys = ['Administrator', 'Moderator', 'Subscriber', 'SuperAdministrator', 'StringTest'];

        $this->assertEquals($expectedKeys, $keys);
    }

    public function testEnumGetValues()
    {
        $values = UserType::getValues();
        $expectedValues = [0, 1, 2, 3, 'TEST'];

        $this->assertEquals($expectedValues, $values);
    }

    public function testEnumGetKey()
    {
        $this->assertEquals('Moderator', UserType::getKey(1));
        $this->assertEquals('SuperAdministrator', UserType::getKey(3));
        $this->assertEquals('StringTest', UserType::getKey('TEST'));
    }

    public function testEnumGetValue()
    {
        $this->assertEquals(1, UserType::getValue('Moderator'));
        $this->assertEquals(3, UserType::getValue('SuperAdministrator'));
        $this->assertEquals('TEST', UserType::getValue('StringTest'));
    }

    public function testEnumGetDescription()
    {
        $this->assertEquals('Moderator', UserType::getDescription(1));
        $this->assertEquals('Super admin', UserType::getDescription(3));
        $this->assertEquals('StringTest', UserType::getDescription('TEST'));
    }
}
