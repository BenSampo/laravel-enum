<?php

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Enum;

final class Described extends Enum
{
    const NO_DESCRIPTION = 0;

    /** just one line */
    const SINGLE_LINE = 1;

    /** more
     * than
     * one
     * line
     */
    const MULTI_LINE = 2;

    /**
     * @deprecated because some reason
     */
    const DEPRECATED = 3;
}
