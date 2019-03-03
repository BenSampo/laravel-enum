<?php

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\EnumInstanceTrait;

final class UserTypeLocalized extends Enum implements LocalizedEnum
{
    use EnumInstanceTrait;

    const Moderator = 0;
    const Administrator = 1;
    const SuperAdministrator = 2;
}
