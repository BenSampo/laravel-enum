<?php

namespace BenSampo\Enum\Tests;

use PHPUnit\Framework\TestCase;
use BenSampo\Enum\Tests\Enums\DescriptionFromAttribute;

class EnumAttributeDescriptionTest extends TestCase
{
    public function test_enum_can_get_description_defined_using_attribute()
    {
        $this->assertSame('Admin', DescriptionFromAttribute::getDescription(DescriptionFromAttribute::Administrator));
        $this->assertSame('Mod (Level 1)', DescriptionFromAttribute::getDescription(DescriptionFromAttribute::Moderator));
    }

    public function test_enum_description_falls_back_to_get_description_method_when_not_defined_using_attribute()
    {
        $this->assertSame('Super Admin', DescriptionFromAttribute::getDescription(DescriptionFromAttribute::SuperAdministrator));
    }
}
