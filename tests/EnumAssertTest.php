<?php

namespace BenSampo\Enum\Tests;

use PHPUnit\Framework\TestCase;
use BenSampo\Enum\Tests\Enums\UserType;
use BenSampo\Enum\Tests\Enums\StringValues;
use BenSampo\Enum\Exceptions\InvalidEnumMemberException;

class EnumAssertTest extends TestCase
{
    public function test_can_instantiate_enum_class()
    {
        $userType = UserType::assert(UserType::Administrator);
        $this->assertInstanceOf(UserType::class, $userType);

        $stringValues = new StringValues(StringValues::Moderator);
        $this->assertInstanceOf(StringValues::class, $stringValues);
    }

    public function test_an_exception_is_thrown_when_trying_to_instantiate_enum_class_with_an_invalid_enum_value()
    {
        $this->expectException(InvalidEnumMemberException::class);

        UserType::assert('InvalidValue');
    }

    public function test_can_get_the_value_for_an_enum_instance()
    {
        $userType = UserType::assert(UserType::Administrator);

        $this->assertEquals($userType->value, UserType::Administrator);
    }

    public function test_can_get_the_key_for_an_enum_instance()
    {
        $userType = UserType::assert(UserType::Administrator);

        $this->assertEquals($userType->key, UserType::getKey(UserType::Administrator));
    }

    public function test_can_get_the_description_for_an_enum_instance()
    {
        $userType = UserType::assert(UserType::Administrator);

        $this->assertEquals($userType->description, UserType::getDescription(UserType::Administrator));
    }
}
