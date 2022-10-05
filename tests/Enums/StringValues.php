<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Enum;

/**
 * @extends Enum<string>
 */
final class StringValues extends Enum
{
    const Administrator = 'administrator';
    const Moderator = 'moderator';
}
