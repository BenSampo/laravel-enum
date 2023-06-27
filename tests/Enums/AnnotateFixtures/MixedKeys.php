<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Enums\Annotate;

use BenSampo\Enum\Enum;

/**
 * @method static static Normal()
 * @method static static MultiWordKeyName()
 * @method static static UPPERCASE()
 * @method static static UPPERCASE_SNAKE_CASE()
 * @method static static lowercase_snake_case()
 * @method static static UPPERCASE_SNAKE_CASE_NUMERIC_SUFFIX_2()
 * @method static static lowercase_snake_case_numeric_suffix_2()
 * @extends Enum<self::*>
 */
final class MixedKeys extends Enum
{
    const Normal = 1;
    const MultiWordKeyName = 2;
    const UPPERCASE = 3;
    const UPPERCASE_SNAKE_CASE = 4;
    const lowercase_snake_case = 5;
    const UPPERCASE_SNAKE_CASE_NUMERIC_SUFFIX_2 = 6;
    const lowercase_snake_case_numeric_suffix_2 = 7;
}
