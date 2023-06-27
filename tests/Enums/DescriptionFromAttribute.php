<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Attributes\Description;
use BenSampo\Enum\Enum;

/**
 * @extends Enum<self::*>
 */
#[Description('Enum description')]
final class DescriptionFromAttribute extends Enum
{
    #[Description('Admin')]
    public const Administrator = 0;

    #[Description('Mod (Level 1)')]
    public const Moderator = 1;

    public const Subscriber = 2;

    public const SuperAdministrator = 3;

    #[Description('First description')]
    // @phpstan-ignore-next-line intentionally wrong
    #[Description('Second description')]
    public const InvalidCaseWithMultipleDescriptions = 4;

    public static function getDescription(mixed $value): string
    {
        if ($value === self::SuperAdministrator) {
            return 'Super Admin';
        }

        return parent::getDescription($value);
    }
}
