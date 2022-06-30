<?php

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Tests\Enums\InvalidMultipleClassDescriptionFromAttribute;
use Exception;
use PHPUnit\Framework\TestCase;
use BenSampo\Enum\Tests\Enums\DescriptionFromAttribute;

class EnumAttributeDescriptionTest extends TestCase
{
    public function test_enum_can_get_class_description_defined_using_attribute()
    {
        $this->assertSame('Enum description', DescriptionFromAttribute::getClassDescription());
    }

    public function test_an_exception_is_thrown_when_accessing_a_class_description_which_is_annotated_with_multiple_description_attributes()
    {
        $this->expectException(Exception::class);

        InvalidMultipleClassDescriptionFromAttribute::getClassDescription();
    }

    public function test_enum_can_get_value_description_defined_using_attribute()
    {
        $this->assertSame('Admin', DescriptionFromAttribute::getDescription(DescriptionFromAttribute::Administrator));
        $this->assertSame('Mod (Level 1)', DescriptionFromAttribute::getDescription(DescriptionFromAttribute::Moderator));
    }

    public function test_enum_description_falls_back_to_get_description_method_when_not_defined_using_attribute()
    {
        $this->assertSame('Super Admin', DescriptionFromAttribute::getDescription(DescriptionFromAttribute::SuperAdministrator));
    }

    public function test_an_exception_is_thrown_when_accessing_a_description_which_is_annotated_with_multiple_description_attributes()
    {
        $this->expectException(Exception::class);

        DescriptionFromAttribute::InvalidCaseWithMultipleDescriptions()->description;
    }

    public function test_an_exception_is_not_thrown_when_accessing_a_description_for_an_invalid_value()
    {
        $this->assertSame('', DescriptionFromAttribute::getDescription('invalid'));
    }
}
