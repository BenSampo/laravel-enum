<?php

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

    public function test_can_use_validation_helpers_on_enum_class()
    {
        $keyRule = StringValues::validateKey();
        $strictValueRule = StringValues::validateValue();
        $permissiveValueRule = StringValues::validateValue(false);
        $instanceRule = StringValues::validateInstance();

        $class_with_to_string = new class {

            public function __toString()
            {
                return 'administrator';
            }
        };

        $this->assertTrue($keyRule->passes('', 'Administrator'));
        $this->assertFalse($keyRule->passes('', 'NotAValidUserType'));

        $this->assertTrue($strictValueRule->passes('', 'administrator'));
        $this->assertFalse($strictValueRule->passes('', 'not_a_valid_user_type'));
        $this->assertFalse($strictValueRule->passes('', $class_with_to_string));

        $this->assertTrue($permissiveValueRule->passes('', 'administrator'));
        $this->assertFalse($permissiveValueRule->passes('', 'not_a_valid_user_type'));
        $this->assertTrue($permissiveValueRule->passes('', $class_with_to_string));

        $this->assertTrue($instanceRule->passes('', StringValues::Administrator()));
        $this->assertFalse($instanceRule->passes('', 'Administrator'));
        $this->assertFalse($instanceRule->passes('', 'administrator'));
        $this->assertFalse($instanceRule->passes('', $class_with_to_string));
        $this->assertFalse($instanceRule->passes('', UserType::Administrator()));
    }
}
