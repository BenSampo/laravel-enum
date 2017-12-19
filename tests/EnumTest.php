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
        $this->assertEquals(UserType::getKey(1), 'Moderator');
        $this->assertEquals(UserType::getKey(3), 'SuperAdministrator');
    }

    public function testEnumGetValue()
    {
        $this->assertEquals(UserType::getValue('Moderator'), 1);
        $this->assertEquals(UserType::getValue('SuperAdministrator'), 3);
    }

    public function testEnumGetDescription()
    {
        $this->assertEquals(UserType::getDescription(1), 'Moderator');
        $this->assertEquals(UserType::getDescription(3), 'Super admin');
    }
}
