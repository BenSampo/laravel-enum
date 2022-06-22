<?php declare(strict_types=1);

namespace BenSampo\Enum;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * This type allows you to call ->change() on an enum column definition
 * when using migrations.
 */
class EnumType extends Type
{
    const ENUM = 'enum';

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param  array<mixed>  $column The column definition.
     * @param  \Doctrine\DBAL\Platforms\AbstractPlatform  $platform The currently used database platform.
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $values = implode(
            ',',
            array_map(
                function (string $value): string {
                    return "'$value'";
                },
                $column['allowed']
            )
        );

        return "ENUM($values)";
    }

    /**
     * Gets the name of this type.
     */
    public function getName(): string
    {
        return self::ENUM;
    }

    /**
     * @return array<string>
     */
    public function getMappedDatabaseTypes(AbstractPlatform $platform): array
    {
        return [
            self::ENUM,
        ];
    }
}
