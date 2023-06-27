<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Enums\Annotate;

use BenSampo\Enum\Enum;

/**
 * This is a test enum with multiple line comments.
 *
 * Test enum with multiple line comments. Test enum
 * with multiple line comments. Test enum with multiple line comments.
 *
 * Test enum with multiple line comments.
 *
 * @extends Enum<self::*>
 */
final class EnumWithMultipleLineComments extends Enum
{
    const A = 1;
    const B = 2;
    const C = 3;
}
