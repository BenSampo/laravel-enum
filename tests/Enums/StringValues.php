<?php

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\EnumInstanceTrait;

final class StringValues extends Enum
{
    use EnumInstanceTrait;

    const Administrator = 'administrator';
    const Moderator = 'moderator';
}
