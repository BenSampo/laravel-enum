<?php

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Enum;

final class UserType extends Enum
{
    const Administrator = 0;
    const Moderator = 1;
    const Subscriber = 2;
    const SuperAdministrator = 3;
    const StringTest = 'TEST';

    public static function getDescription(int $value): string
    {
        switch ($value) {
            case self::SuperAdministrator:
                return 'Super admin';
            break;
            default:
                return self::getKey($value);
        }
    }
}
