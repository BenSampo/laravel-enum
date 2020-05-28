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

    public function test_can_get_the_value_for_an_enum_instance()
    {
        $userType = UserType::getInstance(UserType::Administrator);

        $this->assertEquals($userType->value, UserType::Administrator);
    }

    public function test_can_get_the_key_for_an_enum_instance()
    {
        $userType = UserType::getInstance(UserType::Administrator);

        $this->assertEquals($userType->key, UserType::getKey(UserType::Administrator));
    }

    public function test_can_get_the_description_for_an_enum_instance()
    {
        $userType = UserType::getInstance(UserType::Administrator);

        $this->assertEquals($userType->description, UserType::getDescription(UserType::Administrator));
    }

    public function test_can_get_enum_instance_by_calling_an_enum_key_as_a_static_method()
    {
        $this->assertInstanceOf(UserType::class, UserType::Administrator());
    }

    public function test_magic_instantiation_from_instance_method()
    {
        $userType = new UserType(UserType::Administrator);
        $this->assertInstanceOf(UserType::class, $userType->magicInstantiationFromInstanceMethod());
    }

    public function test_an_exception_is_thrown_when_trying_to_get_enum_instance_by_calling_an_enum_key_as_a_static_method_which_does_not_exist()
    {
        $this->expectException(\BadMethodCallException::class);

        UserType::KeyWhichDoesNotExist();
    }

    public function test_getting_an_instance_using_an_instance_returns_an_instance()
    {
        $this->assertInstanceOf(UserType::class, UserType::getInstance(UserType::Administrator));
    }
}
