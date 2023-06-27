<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Rules\Enum;
use BenSampo\Enum\Tests\Enums\UserType;
use PHPUnit\Framework\TestCase;

final class EnumValidationTest extends TestCase
{
    public function testValidationPasses(): void
    {
        $passes1 = (new Enum(UserType::class))->passes('', UserType::Administrator());

        $this->assertTrue($passes1);
    }

    public function testValidationFails(): void
    {
        $fails1 = (new Enum(UserType::class))->passes('', 'Some string');
        $fails2 = (new Enum(UserType::class))->passes('', 1);
        $fails3 = (new Enum(UserType::class))->passes('', UserType::Administrator()->key);
        $fails4 = (new Enum(UserType::class))->passes('', UserType::Administrator()->value);

        $this->assertFalse($fails1);
        $this->assertFalse($fails2);
        $this->assertFalse($fails3);
        $this->assertFalse($fails4);
    }

    public function testAnExceptionIsThrownIfAnNonExistingClassIsPassed(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        // @phpstan-ignore-next-line intentionally wrong
        new Enum('PathToAClassThatDoesntExist');
    }

    public function testCanSerializeToString(): void
    {
        $rule = new Enum(UserType::class);

        $this->assertSame('enum:' . UserType::class, (string) $rule);
    }
}
