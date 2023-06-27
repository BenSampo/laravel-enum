<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Enum;

/**
 * @extends Enum<int>
 */
final class UserTypeCustomCast extends Enum
{
    public const Administrator = 0;
    public const Moderator = 1;
    public const Subscriber = 2;
    public const SuperAdministrator = 3;

    public static function parseDatabase(mixed $value): mixed
    {
        $parts = explode('-', $value);
        $databaseValue = $parts[1] ?? null;

        return $databaseValue
            ? (int) $databaseValue
            : null;
    }

    public static function serializeDatabase(mixed $value): mixed
    {
        return "type-{$value}";
    }
}
