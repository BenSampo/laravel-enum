<?php

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\EnumInstanceTrait;

final class MixedKeyFormats extends Enum
{
    use EnumInstanceTrait;

    const Normal = 1;
    const MultiWordKeyName = 2;
    const UPPERCASE = 3;
    const UPPERCASE_SNAKE_CASE = 4;
    const lowercase_snake_case = 5;
}
