<?php

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Enum;

final class AnnotatedConstants extends Enum
{
    /**
     * Internal and deprecated
     *
     * @internal
     *
     * @deprecated 1.0 Deprecation description
     */
    const InternalDeprecated = 0;
    /**
     * Internal
     *
     * @internal
     */
    const Internal = 1;
    /**
     * Deprecated
     *
     * @deprecated
     */
    const Deprecated = 2;
    const Unannotated = 3;
}
