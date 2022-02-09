<?php

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Attributes\Description;

final class DescriptionFromAttribute extends Enum
{
    #[Description('Admin')]
    const Administrator = 0;

    #[Description('Mod (Level 1)')]
    const Moderator = 1;

    const Subscriber = 2;

    const SuperAdministrator = 3;

    #[Description('First description')]
    #[Description('Second description')]
    const InvalidCaseWithMultipleDescriptions = 4;

    public static function getDescription($value): string
    {
        if ($value === self::SuperAdministrator) {
            return 'Super Admin';
        }

        return parent::getDescription($value);
    }
}
