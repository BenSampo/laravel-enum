<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Rules\EnumValue;
use BenSampo\Enum\Tests\Enums\StringValues;
use BenSampo\Enum\Tests\Enums\SuperPowers;
use BenSampo\Enum\Tests\Enums\UserType;
use PHPUnit\Framework\TestCase;

final class EnumValueTest extends TestCase
{
    public function testValidationPasses(): void
    {
        $passes1 = (new EnumValue(UserType::class))->passes('', 3);
        $passes2 = (new EnumValue(StringValues::class))->passes('', 'administrator');

        $this->assertTrue($passes1);
        $this->assertTrue($passes2);
    }

    public function testValidationFails(): void
    {
        $fails1 = (new EnumValue(UserType::class))->passes('', 7);
        $fails2 = (new EnumValue(UserType::class))->passes('', 'OtherString');
        $fails3 = (new EnumValue(UserType::class))->passes('', '3');

        $this->assertFalse($fails1);
        $this->assertFalse($fails2);
        $this->assertFalse($fails3);
    }

    public function testFlaggedEnumPassesWithNoFlagsSet(): void
    {
        $passed = (new EnumValue(SuperPowers::class))->passes('', 0);

        $this->assertTrue($passed);
    }

    public function testFlaggedEnumPassesWithSingleFlagSet(): void
    {
        $passed = (new EnumValue(SuperPowers::class))->passes('', SuperPowers::Flight);

        $this->assertTrue($passed);
    }

    public function testFlaggedEnumPassesWithMultipleFlagsSet(): void
    {
        $passed = (new EnumValue(SuperPowers::class))->passes('', SuperPowers::Superman);

        $this->assertTrue($passed);
    }

    public function testFlaggedEnumPassesWithAllFlagsSet(): void
    {
        $allFlags = array_reduce(SuperPowers::getValues(), function (int $carry, int $powerValue) {
            return $carry | $powerValue;
        }, 0);
        $passed = (new EnumValue(SuperPowers::class))->passes('', $allFlags);

        $this->assertTrue($passed);
    }

    public function testFlaggedEnumFailsWithInvalidFlagSet(): void
    {
        $allFlagsSet = array_reduce(SuperPowers::getValues(), function ($carry, $value) {
            return $carry | $value;
        }, 0);
        $passed = (new EnumValue(SuperPowers::class))->passes('', $allFlagsSet + 1);

        $this->assertFalse($passed);
    }

    public function testCanTurnOffStrictTypeChecking(): void
    {
        $passes = (new EnumValue(UserType::class, false))->passes('', '3');

        $this->assertTrue($passes);

        $fails1 = (new EnumValue(UserType::class, false))->passes('', '10');
        $fails2 = (new EnumValue(UserType::class, false))->passes('', 'a');

        $this->assertFalse($fails1);
        $this->assertFalse($fails2);
    }

    public function testAnExceptionIsThrownIfAnNonExistingClassIsPassed(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new EnumValue('PathToAClassThatDoesntExist'))->passes('', 'Test');
    }

    public function testCanSerializeToStringWithoutStrictTypeChecking(): void
    {
        $rule = new EnumValue(UserType::class, false);

        $this->assertSame('enum_value:' . UserType::class . ',false', (string) $rule);
    }

    public function testCanSerializeToStringWithStrictTypeChecking(): void
    {
        $rule = new EnumValue(UserType::class, true);

        $this->assertSame('enum_value:' . UserType::class . ',true', (string) $rule);
    }
}
