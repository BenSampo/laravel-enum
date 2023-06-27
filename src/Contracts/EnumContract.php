<?php declare(strict_types=1);

namespace BenSampo\Enum\Contracts;

interface EnumContract
{
    /** Determine if this instance is equivalent to a given value. */
    public function is(mixed $enumValue): bool;
}
