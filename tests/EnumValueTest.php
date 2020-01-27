<?php

namespace BenSampo\Enum\Tests;

use PHPUnit\Framework\TestCase;
use BenSampo\Enum\Rules\EnumValue;
use BenSampo\Enum\Tests\Enums\UserType;
use BenSampo\Enum\Tests\Enums\StringValues;

class EnumValueTest extends TestCase
{
    public function test_validation_passes()
    {
        $passes1 = (new EnumValue(UserType::class))->passes('', 3);
        $passes2 = (new EnumValue(StringValues::class))->passes('', 'administrator');

        $this->assertTrue($passes1);
        $this->assertTrue($passes2);
    }

    public function test_validation_fails()
    {
        $fails1 = (new EnumValue(UserType::class))->passes('', 7);
        $fails2 = (new EnumValue(UserType::class))->passes('', 'OtherString');
        $fails3 = (new EnumValue(UserType::class))->passes('', '3');

        $this->assertFalse($fails1);
        $this->assertFalse($fails2);
        $this->assertFalse($fails3);
    }

    public function test_can_turn_off_strict_type_checking()
    {
        $passes = (new EnumValue(UserType::class, false))->passes('', '3');

        $this->assertTrue($passes);

        $fails1 = (new EnumValue(UserType::class, false))->passes('', '10');
        $fails2 = (new EnumValue(UserType::class, false))->passes('', 'a');

        $this->assertFalse($fails1);
        $this->assertFalse($fails2);
    }

    public function test_an_exception_is_thrown_if_an_non_existing_class_is_passed()
    {
        $this->expectException(\InvalidArgumentException::class);

        (new EnumValue('PathToAClassThatDoesntExist'))->passes('', 'Test');
    }
    
    public function test_can_serialize_to_string_without_strict_type_checking()
    {
        $rule = new EnumValue(UserType::class, false);
    
        $this->assertEquals('enum_value:' . UserType::class . ',false', (string) $rule);
    }
    
    public function test_can_serialize_to_string_with_strict_type_checking()
    {
        $rule = new EnumValue(UserType::class, true);
        
        $this->assertEquals('enum_value:' . UserType::class . ',true', (string) $rule);
    }
}
