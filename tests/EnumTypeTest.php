<?php

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\EnumType;
use BenSampo\Enum\Tests\Enums\StringValues;
use Doctrine\DBAL\Platforms\MySQL57Platform;
use Doctrine\DBAL\Types\Type;

class EnumTypeTest extends ApplicationTestCase
{
    public function testIsRegistered(): void
    {
        $this->assertInstanceOf(
            EnumType::class,
            Type::getType(EnumType::ENUM)
        );
    }

    public function testGetSQLDeclaration(): void
    {
        $enumType = Type::getType(EnumType::ENUM);

        $this->assertSame(
            "ENUM('administrator','moderator')",
            $enumType->getSQLDeclaration(
                ['allowed' => StringValues::getValues()],
                new MySQL57Platform()
            )
        );
    }
}
