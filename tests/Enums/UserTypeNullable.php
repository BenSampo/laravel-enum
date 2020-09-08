<?php

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Contracts\NullableEnum;
use BenSampo\Enum\Enum;

final class UserTypeNullable extends Enum implements NullableEnum
{
    const Moderator = 0;
    const Administrator = 1;
    const SuperAdministrator = 2;
}
