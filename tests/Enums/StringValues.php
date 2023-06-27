<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Enum;

/**
 * @extends Enum<self::*>
 */
final class StringValues extends Enum
{
    public const Administrator = 'administrator';
    public const Moderator = 'moderator';
}
