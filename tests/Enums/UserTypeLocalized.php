<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * @extends Enum<self::*>
 */
final class UserTypeLocalized extends Enum implements LocalizedEnum
{
    const Moderator = 0;
    const Administrator = 1;
    const SuperAdministrator = 2;
}
