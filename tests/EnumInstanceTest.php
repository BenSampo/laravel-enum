<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Exceptions\InvalidEnumKeyException;
use PHPUnit\Framework\TestCase;
use BenSampo\Enum\Tests\Enums\UserType;
use BenSampo\Enum\Exceptions\InvalidEnumMemberException;

final class EnumInstanceTest extends TestCase
{
    public function test_can_instantiate_enum_class_with_new(): void
    {
        $userType = new UserType(UserType::Administrator);
        $this->assertInstanceOf(UserType::class, $userType);
    }

    public function test_can_instantiate_enum_class_from_value(): void
    {
        $userType = UserType::fromValue(UserType::Administrator);
        $this->assertInstanceOf(UserType::class, $userType);
    }

    public function test_can_instantiate_enum_class_from_key(): void
    {
        $userType = UserType::fromKey('Administrator');
        $this->assertInstanceOf(UserType::class, $userType);
    }

    public function test_an_exception_is_thrown_when_trying_to_instantiate_enum_class_with_an_invalid_enum_value(): void
    {
        $this->expectException(InvalidEnumMemberException::class);
        // @phpstan-ignore-next-line intentionally wrong
        UserType::fromValue('InvalidValue');
    }

    public function test_an_exception_is_thrown_when_trying_to_instantiate_enum_class_with_an_invalid_enum_key(): void
    {
        $this->expectException(InvalidEnumKeyException::class);
        UserType::fromKey('foobar');
    }

    public function test_can_get_the_value_for_an_enum_instance(): void
    {
        $userType = UserType::fromValue(UserType::Administrator);

        $this->assertSame($userType->value, UserType::Administrator);
    }

    public function test_can_get_the_key_for_an_enum_instance(): void
    {
        $userType = UserType::fromValue(UserType::Administrator);

        $this->assertSame($userType->key, UserType::getKey(UserType::Administrator));
    }

    public function test_can_get_the_description_for_an_enum_instance(): void
    {
        $userType = UserType::fromValue(UserType::Administrator);

        $this->assertSame($userType->description, UserType::getDescription(UserType::Administrator));
    }

    public function test_can_get_enum_instance_by_calling_an_enum_key_as_a_static_method(): void
    {
        $this->assertInstanceOf(UserType::class, UserType::Administrator());
    }

    public function test_magic_instantiation_from_instance_method(): void
    {
        $userType = new UserType(UserType::Administrator);
        $this->assertInstanceOf(UserType::class, $userType->magicInstantiationFromInstanceMethod());
    }

    public function test_an_exception_is_thrown_when_trying_to_get_enum_instance_by_calling_an_enum_key_as_a_static_method_which_does_not_exist(): void
    {
        $this->expectException(InvalidEnumKeyException::class);

        // @phpstan-ignore-next-line intentionally wrong
        UserType::KeyWhichDoesNotExist();
    }

    public function test_getting_an_instance_using_an_instance_returns_an_instance(): void
    {
        $this->assertInstanceOf(UserType::class, UserType::fromValue(UserType::Administrator));
    }
}
