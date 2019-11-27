<?php

namespace BenSampo\Enum\Tests;

use PHPUnit\Framework\TestCase;
use BenSampo\Enum\Rules\EnumKey;
use BenSampo\Enum\Tests\Enums\UserType;
use BenSampo\Enum\Tests\Enums\StringValues;

class EnumKeyTest extends TestCase
{
    public function test_validation_passes()
    {
        $passes1 = (new EnumKey(UserType::class))->passes('', 'Administrator');
        $passes2 = (new EnumKey(StringValues::class))->passes('', 'Administrator');
        $passes3 = (new EnumKey(StringValues::class))->passes('', 'administrator');
        $passes4 = (new EnumKey(UserType::class))->passes('', UserType::getInstance(UserType::Subscriber));

        $this->assertTrue($passes1);
        $this->assertTrue($passes2);
        $this->assertFalse($passes3);
        $this->assertTrue($passes4);
    }

    public function test_validation_fails()
    {
        $fails1 = (new EnumKey(UserType::class))->passes('', 'Anything else');
        $fails2 = (new EnumKey(UserType::class))->passes('', 2);
        $fails3 = (new EnumKey(UserType::class))->passes('', '2');

        $this->assertFalse($fails1);
        $this->assertFalse($fails2);
        $this->assertFalse($fails3);
    }

    public function test_an_exception_is_thrown_if_an_non_existing_class_is_passed()
    {
        $this->expectException(\InvalidArgumentException::class);

        (new EnumKey('PathToAClassThatDoesntExist'))->passes('', 'Test');
    }

    public function test_can_serialize_to_string()
    {
        $rule = new EnumKey(UserType::class);

        $this->assertEquals('enum_key:' . UserType::class, (string) $rule);
    }
}
