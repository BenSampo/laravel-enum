<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Enum;

/**
 * @extends Enum<string>
 */
final class SingleValue extends Enum
{
    const KEY = 'value';
}
