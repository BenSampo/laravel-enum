<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Enums\Annotate;

use BenSampo\Enum\Enum;

/**
 * @extends Enum<self::*>
 */
final class AnnotateTestOneEnum extends Enum
{
    const Administrator = 'administrator';
    const Moderator = 'moderator';
}
