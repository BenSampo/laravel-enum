<?php

namespace BenSampo\Enum\Tests;

use PHPUnit\Framework\TestCase;
use BenSampo\Enum\Tests\Enums\UserType;
use BenSampo\Enum\Tests\Enums\StringValues;
use BenSampo\Enum\Exceptions\InvalidEnumMemberException;

class EnumInstanceTest extends TestCase
{
    public function test_can_instantiate_enum_class()
    {
        $userType = UserType::getInstance(UserType::Administrator);
        $this->assertInstanceOf(UserType::class, $userType);

        $stringValues = new StringValues(StringValues::Moderator);
        $this->assertInstanceOf(StringValues::class, $stringValues);
    }

    public function test_an_exception_is_thrown_when_trying_to_instantiate_enum_class_with_an_invalid_enum_value()
    {
        $this->expectException(InvalidEnumMemberException::class);

        UserType::getInstance('InvalidValue');
    }

    public function test_instance_can_check_it_is_set_to_an_enum_value()
    {
        $userType = UserType::getInstance(UserType::Administrator);

        $this->assertTrue($userType->is(UserType::Administrator));
        $this->assertFalse($userType->is(UserType::SuperAdministrator));
    }

    public function test_an_exception_is_thrown_when_trying_to_check_an_enum_instance_value_with_an_invalid_value()
    {
        $this->expectException(InvalidEnumMemberException::class);

        StringValues::getInstance(UserType::Subscriber)->is('InvalidValue');
    }
}
