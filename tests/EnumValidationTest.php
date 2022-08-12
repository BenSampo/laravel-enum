<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Rules\Enum;
use PHPUnit\Framework\TestCase;
use BenSampo\Enum\Tests\Enums\UserType;

class EnumValidationTest extends TestCase
{
    public function test_validation_passes()
    {
        $passes1 = (new Enum(UserType::class))->passes('', UserType::Administrator());

        $this->assertTrue($passes1);
    }

    public function test_validation_fails()
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

    public function test_an_exception_is_thrown_if_an_non_existing_class_is_passed()
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Enum('PathToAClassThatDoesntExist'))->passes('', 'Test');
    }

    public function test_can_serialize_to_string()
    {
        $rule = new Enum(UserType::class);

        $this->assertSame('enum:' . UserType::class, (string) $rule);
    }
}
