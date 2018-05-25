<?php

namespace BenSampo\Enum\Tests;

use PHPUnit\Framework\TestCase;
use BenSampo\Enum\Rules\EnumValue;

class EnumValueTest extends TestCase
{
    public function testValidationPasses()
    {
        $passes1 = (new EnumValue(UserType::class))->passes('', 3);
        $passes2 = (new EnumValue(UserType::class))->passes('', 'TEST');

        $this->assertTrue($passes1);
        $this->assertTrue($passes2);
    }

    public function testValidationFails()
    {
        $fails1 = (new EnumValue(UserType::class))->passes('', 7);
        $fails2 = (new EnumValue(UserType::class))->passes('', 'otherstring');
        $fails3 = (new EnumValue(UserType::class))->passes('', '3');

        $this->assertFalse($fails1);
        $this->assertFalse($fails2);
        $this->assertFalse($fails3);
    }
}
