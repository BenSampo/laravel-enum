<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Enums\Annotate;

use BenSampo\Enum\Enum;

/**
 * @extends Enum<self::*>
 */
final class MixedKeys extends Enum
{
    public const Normal = 1;
    public const MultiWordKeyName = 2;
    public const UPPERCASE = 3;
    public const UPPERCASE_SNAKE_CASE = 4;
    public const lowercase_snake_case = 5;
    public const UPPERCASE_SNAKE_CASE_NUMERIC_SUFFIX_2 = 6;
    public const lowercase_snake_case_numeric_suffix_2 = 7;
}
