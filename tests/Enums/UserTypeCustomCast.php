<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Enum;

/**
 * @extends Enum<self::*>
 */
final class UserTypeCustomCast extends Enum
{
    const Administrator = 0;
    const Moderator = 1;
    const Subscriber = 2;
    const SuperAdministrator = 3;

    public static function parseDatabase(mixed $value): mixed
    {
        $databaseValue = explode('-', $value)[1] ?? null;

        return $databaseValue
            ? (int) $databaseValue
            : null;
    }

    public static function serializeDatabase(mixed $value): mixed
    {
        return "type-{$value}";
    }
}
