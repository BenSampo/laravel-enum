<?php

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Enum;

final class UserTypeWithLocale extends Enum
{
    protected static $localizationKey = 'enums.user-type';

    const Administrator = 0;
    const Moderator = 1;
    const Subscriber = 2;
    const SuperAdministrator = 3;
    const FourWordKeyName = 4;
    const UPPERCASE = 5;
    const StringKey = 'StringValue';
}
