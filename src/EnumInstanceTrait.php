<?php

namespace BenSampo\Enum;

trait EnumInstanceTrait
{

    /**
     * Return an enum instance so that it can be used as
     * typehint on methods, functions or constructors.
     *
     * @param mixed $value
     * @return self
     */
    public static function getInstance($value): self
    {
        static::validateValue($value);
        return new self($value);
    }
}
