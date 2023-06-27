<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Enum;

/**
 * @extends Enum<self::*>
 */
final class IntegerValues extends Enum
{
    public const B = 1;
    public const A = 2;
}
