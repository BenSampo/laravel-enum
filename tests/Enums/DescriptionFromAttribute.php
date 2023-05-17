<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Attributes\Description;

/**
 * @extends Enum<self::*>
 */
#[Description('Enum description')]
final class DescriptionFromAttribute extends Enum
{
    #[Description('Admin')]
    const Administrator = 0;

    #[Description('Mod (Level 1)')]
    const Moderator = 1;

    const Subscriber = 2;

    const SuperAdministrator = 3;

    #[Description('First description')]
    // @phpstan-ignore-next-line intentionally wrong
    #[Description('Second description')]
    const InvalidCaseWithMultipleDescriptions = 4;

    public static function getDescription(mixed $value): string
    {
        if ($value === self::SuperAdministrator) {
            return 'Super Admin';
        }

        return parent::getDescription($value);
    }
}
