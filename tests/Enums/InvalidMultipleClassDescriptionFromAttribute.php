<?php

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Attributes\Description;

#[Description('First Enum description')]
// @phpstan-ignore-next-line intentionally wrong
#[Description('Second Enum description')]
final class InvalidMultipleClassDescriptionFromAttribute extends Enum
{
    const Administrator = 0;

    const Moderator = 1;
}
