<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Enum;

/**
 * This is a test enum with single line comment.
 *
 * @method static static A()
 * @method static static B()
 * @method static static C()
 * @extends Enum<self::*>
 */
final class EnumWithSingleLineComment extends Enum
{
    const A = 1;
    const B = 2;
    const C = 3;
}
