<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * @extends Enum<self::*>
 */
final class UserTypeLocalized extends Enum implements LocalizedEnum
{
    public const Moderator = 0;
    public const Administrator = 1;
    public const SuperAdministrator = 2;
}
