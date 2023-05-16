<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Attributes\Description;

/**
 * @extends Enum<self::*>
 */
#[Description('First Enum description')]
// @phpstan-ignore-next-line intentionally wrong
#[Description('Second Enum description')]
final class InvalidMultipleClassDescriptionFromAttribute extends Enum
{
    const Administrator = 0;
    const Moderator = 1;
}
