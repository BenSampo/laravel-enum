<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Enum;

/**
 * @extends Enum<self::*>
 */
class ParentEnum extends Enum
{
    const PARENT = 'parent';
}
