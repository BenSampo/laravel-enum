<?php

namespace BenSampo\Enum\Tests;

use PHPUnit\Framework\TestCase;
use BenSampo\Enum\Rules\EnumValue;

class EnumValueTest extends TestCase
{
    public function testValidationPasses()
    {
        $passes = (new EnumValue(UserType::class))->passes('', 3);

        $this->assertTrue($passes);
    }

    public function testValidationFails()
    {
        $passes1 = (new EnumValue(UserType::class))->passes('', 7);
        $passes2 = (new EnumValue(UserType::class))->passes('', 'test');
        $passes3 = (new EnumValue(UserType::class))->passes('', '3');

        $this->assertFalse($passes1);
        $this->assertFalse($passes2);
        $this->assertFalse($passes3);
    }
}
