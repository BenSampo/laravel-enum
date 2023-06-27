<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Enums\ToNative;

use BenSampo\Enum\Enum;

/**
 * @extends Enum<self::*>
 */
final class UserType extends Enum
{
    public const Administrator = 0;
    public const Moderator = 1;
}
