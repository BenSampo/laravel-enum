<?php

namespace BenSampo\Enum\Contracts;

interface EnumContract
{
    /**
     * Determine if this instance is equivalent to a given value.
     *
     * @param  mixed  $enumValue
     * @return bool
     */
    public function is($enumValue): bool;
}
