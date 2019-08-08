<?php

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Exceptions\InvalidEnumMemberException;
use BenSampo\Enum\Tests\Enums\StringValues;
use BenSampo\Enum\Tests\Enums\UserType;
use PHPUnit\Framework\TestCase;

class EnumComparisonTest extends TestCase
{
    public function test_comparison_against_constant_matching()
    {
        $admin = UserType::getInstance(UserType::Administrator);

        $this->assertTrue($admin->is(UserType::Administrator));
    }

    public function test_comparison_against_constant_not_matching()
    {
        $admin = UserType::getInstance(UserType::Administrator);

        $this->assertFalse($admin->is(UserType::SuperAdministrator));
    }

    public function test_comparison_against_itself_matches()
    {
        $admin = UserType::getInstance(UserType::Administrator);

        $this->assertTrue($admin->is($admin));
    }

    public function test_comparison_against_other_instances_matches()
    {
        $admin = UserType::getInstance(UserType::Administrator);

        $this->assertTrue($admin->is($admin));
    }

    public function test_comparison_against_other_instances_not_matching()
    {
        $admin = UserType::getInstance(UserType::Administrator);
        $superAdmin = UserType::getInstance(UserType::SuperAdministrator);

        $this->assertFalse($admin->is($superAdmin));
    }

    public function test_an_exception_is_thrown_when_trying_to_check_an_enum_instance_value_with_an_invalid_value()
    {
        $this->expectException(InvalidEnumMemberException::class);

        StringValues::getInstance(UserType::Subscriber)->is('InvalidValue');
    }
}
