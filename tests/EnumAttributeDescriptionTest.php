<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Exceptions\InvalidEnumMemberException;
use BenSampo\Enum\Tests\Enums\DescriptionFromAttribute;
use BenSampo\Enum\Tests\Enums\InvalidMultipleClassDescriptionFromAttribute;
use PHPUnit\Framework\TestCase;

final class EnumAttributeDescriptionTest extends TestCase
{
    public function testEnumCanGetClassDescriptionDefinedUsingAttribute(): void
    {
        $this->assertSame('Enum description', DescriptionFromAttribute::getClassDescription());
    }

    public function testAnExceptionIsThrownWhenAccessingAClassDescriptionWhichIsAnnotatedWithMultipleDescriptionAttributes(): void
    {
        $this->expectException(\Exception::class);

        InvalidMultipleClassDescriptionFromAttribute::getClassDescription();
    }

    public function testEnumCanGetValueDescriptionDefinedUsingAttribute(): void
    {
        $this->assertSame('Admin', DescriptionFromAttribute::getDescription(DescriptionFromAttribute::Administrator));
        $this->assertSame('Mod (Level 1)', DescriptionFromAttribute::getDescription(DescriptionFromAttribute::Moderator));
    }

    public function testEnumDescriptionFallsBackToGetDescriptionMethodWhenNotDefinedUsingAttribute(): void
    {
        $this->assertSame('Super Admin', DescriptionFromAttribute::getDescription(DescriptionFromAttribute::SuperAdministrator));
    }

    public function testAnExceptionIsThrownWhenAccessingADescriptionWhichIsAnnotatedWithMultipleDescriptionAttributes(): void
    {
        $this->expectException(\Exception::class);

        // @phpstan-ignore-next-line wrongly flagged as no-op
        DescriptionFromAttribute::InvalidCaseWithMultipleDescriptions()->description;
    }

    public function testAnExceptionIsThrownWhenAccessingADescriptionForAnInvalidValue(): void
    {
        $this->expectException(InvalidEnumMemberException::class);
        // @phpstan-ignore-next-line intentionally wrong
        DescriptionFromAttribute::getDescription('invalid');
    }
}
