<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Enum;

/**
 * @extends Enum<self::*>
 */
final class MixedKeyFormats extends Enum
{
    const Normal = 1;
    const MultiWordKeyName = 2;
    const UPPERCASE = 3;
    const UPPERCASE_SNAKE_CASE = 4;
    const lowercase_snake_case = 5;
    const UPPERCASE_SNAKE_CASE_NUMERIC_SUFFIX_2 = 6;
    const lowercase_snake_case_numeric_suffix_2 = 7;
}
