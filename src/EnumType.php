<?php declare(strict_types=1);

namespace BenSampo\Enum;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * This type allows you to call ->change() on an enum column definition when using migrations.
 */
class EnumType extends Type
{
    public const ENUM = 'enum';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $values = implode(
            ',',
            array_map(
                fn (string $value): string => "'{$value}'",
                $column['allowed']
            )
        );

        return "ENUM({$values})";
    }

    public function getName(): string
    {
        return self::ENUM;
    }

    public function getMappedDatabaseTypes(AbstractPlatform $platform): array
    {
        return [self::ENUM];
    }
}
