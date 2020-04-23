<?php

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Enum;

final class UserType extends Enum
{
    protected static $nativeType = 'integer';

    const Administrator = 0;
    const Moderator = 1;
    const Subscriber = 2;
    const SuperAdministrator = 3;
}
