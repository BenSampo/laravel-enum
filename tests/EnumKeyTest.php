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

        $this->assertTrue($passes1);
        $this->assertTrue($passes2);
        $this->assertFalse($passes3);
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
}
