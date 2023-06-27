<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Rules\EnumKey;
use BenSampo\Enum\Tests\Enums\StringValues;
use BenSampo\Enum\Tests\Enums\UserType;
use PHPUnit\Framework\TestCase;

final class EnumKeyTest extends TestCase
{
    public function testValidationPasses(): void
    {
        $passes1 = (new EnumKey(UserType::class))->passes('', 'Administrator');
        $passes2 = (new EnumKey(StringValues::class))->passes('', 'Administrator');
        $passes3 = (new EnumKey(StringValues::class))->passes('', 'administrator');

        $this->assertTrue($passes1);
        $this->assertTrue($passes2);
        $this->assertFalse($passes3);
    }

    public function testValidationFails(): void
    {
        $fails1 = (new EnumKey(UserType::class))->passes('', 'Anything else');
        $fails2 = (new EnumKey(UserType::class))->passes('', 2);
        $fails3 = (new EnumKey(UserType::class))->passes('', '2');

        $this->assertFalse($fails1);
        $this->assertFalse($fails2);
        $this->assertFalse($fails3);
    }

    public function testAnExceptionIsThrownIfAnNonExistingClassIsPassed(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        // @phpstan-ignore-next-line intentionally wrong
        new EnumKey('PathToAClassThatDoesntExist');
    }

    public function testCanSerializeToString(): void
    {
        $rule = new EnumKey(UserType::class);

        $this->assertSame('enum_key:' . UserType::class, (string) $rule);
    }
}
