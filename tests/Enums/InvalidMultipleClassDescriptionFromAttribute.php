<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Attributes\Description;
use BenSampo\Enum\Enum;

/**
 * @extends Enum<self::*>
 */
#[Description('First Enum description')]
// @phpstan-ignore-next-line intentionally wrong
#[Description('Second Enum description')]
final class InvalidMultipleClassDescriptionFromAttribute extends Enum
{
    public const Administrator = 0;
    public const Moderator = 1;
}
