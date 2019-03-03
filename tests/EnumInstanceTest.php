<?php

namespace BenSampo\Enum\Tests;

use PHPUnit\Framework\TestCase;
use BenSampo\Enum\Rules\EnumKey;
use BenSampo\Enum\Tests\Enums\UserType;
use BenSampo\Enum\Tests\Enums\StringValues;
use BenSampo\Enum\Tests\Enums\MixedKeyFormats;

class EnumInstanceTest extends TestCase
{
    public function testPasses()
    {
        $userType = UserType::getInstance(UserType::Administrator);

        $this->assertInstanceOf(UserType::class, $userType);
        $this->assertTrue($userType->equals(UserType::Administrator));
        $this->assertFalse($userType->equals(UserType::SuperAdministrator));
    }

    /**
     * Throws an expection if the given value is invalid.
     *
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testFails()
    {
        $userType = StringValues::getInstance(UserType::Subscriber);
        $stringType = StringValues::getInstance(StringValues::Administrator);

        $stringType->equals(MixedKeyFormats::Normal);
    }


    /**
     * Throws an expection if the given value is invalid.
     *
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testExpectExceptionOnEqualityCheck()
    {
        $stringType = StringValues::getInstance(StringValues::Administrator);
        $stringType->equals(MixedKeyFormats::Normal);
    }
}
