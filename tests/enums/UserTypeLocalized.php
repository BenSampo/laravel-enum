<?php

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Enum;

final class UserTypeLocalized extends Enum
{
    protected static $localizationKey = 'enums.user-type';

    const Moderator = 0;
    const Administrator = 1;
    const SuperAdministrator = 2;
}
