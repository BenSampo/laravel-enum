<?php

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
     * @param  mixed[]  $fieldDeclaration The field declaration.
     * @param  AbstractPlatform  $platform The currently used database platform.
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $values = implode(
            ',',
            array_map(
                function (string $value): string {
                    return "'$value'";
                },
                $fieldDeclaration['allowed']
            )
        );

        return "ENUM($values)";
    }

    /**
     * Gets the name of this type.
     *
     * @return string
     */
    public function getName()
    {
        return self::ENUM;
    }

    /**
     * @param  AbstractPlatform  $platform
     * @return string[]
     */
    public function getMappedDatabaseTypes(AbstractPlatform $platform)
    {
        return [
            self::ENUM,
        ];
    }
}
