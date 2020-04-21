<?php

namespace BenSampo\Enum\Tests;

use PHPUnit\Framework\TestCase;
use BenSampo\Enum\Tests\Enums\UserType;

class EnumTypeHintTest extends TestCase
{
    public function test_can_pass_an_enum_instance_to_a_type_hinted_method()
    {
        $userType1 = UserType::fromValue(UserType::SuperAdministrator);
        $userType2 = UserType::fromValue(UserType::Moderator);

        $this->assertTrue($this->typeHintedMethod($userType1));
        $this->assertFalse($this->typeHintedMethod($userType2));
    }

    private function typeHintedMethod(UserType $userType)
    {
        if ($userType->is(UserType::SuperAdministrator)) {
            return true;
        }

        return false;
    }
}
