<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Enum;

/**
 * @extends Enum<self::*>
 */
final class AnnotatedConstants extends Enum
{
    /**
     * Internal and deprecated.
     *
     * @internal
     *
     * @deprecated 1.0 Deprecation description
     */
    public const InternalDeprecated = 0;
    /**
     * Internal.
     *
     * @internal
     */
    public const Internal = 1;
    /**
     * Deprecated.
     *
     * @deprecated
     */
    public const Deprecated = 2;
    public const Unannotated = 3;
}
