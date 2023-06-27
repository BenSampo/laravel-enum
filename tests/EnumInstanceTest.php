<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Exceptions\InvalidEnumKeyException;
use BenSampo\Enum\Exceptions\InvalidEnumMemberException;
use BenSampo\Enum\Tests\Enums\UserType;
use PHPUnit\Framework\TestCase;

final class EnumInstanceTest extends TestCase
{
    public function testCanInstantiateEnumClassWithNew(): void
    {
        $userType = new UserType(UserType::Administrator);
        $this->assertSame(UserType::Administrator, $userType->value);
    }

    public function testCanInstantiateEnumClassFromValueRaw(): void
    {
        $userType = UserType::fromValue(UserType::Administrator);
        $this->assertSame(UserType::Administrator, $userType->value);
    }

    public function testCanInstantiateEnumClassFromKey(): void
    {
        $userType = UserType::fromKey('Administrator');
        $this->assertSame(UserType::Administrator, $userType->value);
    }

    public function testAnExceptionIsThrownWhenTryingToInstantiateEnumClassWithAnInvalidEnumValue(): void
    {
        $this->expectException(InvalidEnumMemberException::class);
        // @phpstan-ignore-next-line intentionally wrong
        UserType::fromValue('InvalidValue');
    }

    public function testAnExceptionIsThrownWhenTryingToInstantiateEnumClassWithAnInvalidEnumKey(): void
    {
        $this->expectException(InvalidEnumKeyException::class);
        UserType::fromKey('foobar');
    }

    public function testCanGetTheValueForAnEnumInstance(): void
    {
        $userType = UserType::fromValue(UserType::Administrator);

        $this->assertSame($userType->value, UserType::Administrator);
    }

    public function testCanGetTheKeyForAnEnumInstance(): void
    {
        $userType = UserType::fromValue(UserType::Administrator);

        $this->assertSame($userType->key, UserType::getKey(UserType::Administrator));
    }

    public function testCanGetTheDescriptionForAnEnumInstance(): void
    {
        $userType = UserType::fromValue(UserType::Administrator);

        $this->assertSame($userType->description, UserType::getDescription(UserType::Administrator));
    }

    public function testCanGetEnumInstanceByCallingAnEnumKeyAsAStaticMethod(): void
    {
        $this->assertInstanceOf(UserType::class, UserType::Administrator());
    }

    public function testMagicInstantiationFromInstanceMethod(): void
    {
        $userType = new UserType(UserType::Administrator);
        $this->assertSame(UserType::Administrator, $userType->magicInstantiationFromInstanceMethod()->value);
    }

    public function testAnExceptionIsThrownWhenTryingToGetEnumInstanceByCallingAnEnumKeyAsAStaticMethodWhichDoesNotExist(): void
    {
        $this->expectException(InvalidEnumKeyException::class);

        // @phpstan-ignore-next-line intentionally wrong
        UserType::KeyWhichDoesNotExist();
    }
}
