<?php

namespace BenSampo\Enum\Contracts;

interface NullableEnum
{
    /**
     * Check the enum implements the NullableEnum interface.
     *
     * @return bool
     */
    public static function isNullable(): bool;
}
