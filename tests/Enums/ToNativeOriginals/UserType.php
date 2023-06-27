<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Enums\ToNative;

use BenSampo\Enum\Enum;

/**
 * @extends Enum<self::*>
 */
final class UserType extends Enum
{
    const Administrator = 0;
    const Moderator = 1;
}
