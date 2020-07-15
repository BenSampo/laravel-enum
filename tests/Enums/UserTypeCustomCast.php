<?php

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Enum;

final class UserTypeCustomCast extends Enum
{
    const Administrator = 0;
    const Moderator = 1;
    const Subscriber = 2;
    const SuperAdministrator = 3;

    public static function parseDatabase($value)
    {
        if (! $value) {
            return null;
        }

        return explode('-', $value)[1] ?? null;
    }

    public static function serializeDatabase($value)
    {
        if (! $value) {
            return null;
        }

        return 'type-' . $value;
    }
}
