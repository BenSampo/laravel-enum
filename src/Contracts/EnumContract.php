<?php

namespace BenSampo\Enum\Contracts;

interface EnumContract
{
    public function is($enumValue): bool;
}
