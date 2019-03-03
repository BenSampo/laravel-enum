<?php

namespace BenSampo\Enum;

use BenSampo\Enum\Exceptions\InvalidEnumMemberException;

trait EnumInstanceTrait
{
    /**
     * The value of one the enum members.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Return an enum instance
     *
     * @param mixed $enumValue
     * @return self
     */
    public function __construct($enumValue)
    {
        if (!static::hasValue($enumValue)) {
            throw new InvalidEnumMemberException($enumValue, $this);
        }
        
        $this->value = $enumValue;
    }

    /**
     * Return an enum instance
     *
     * @param mixed $enumValue
     * @return self
     */
    public static function getInstance($enumValue): self
    {
        return new self($enumValue);
    }

    /**
     * Checks the equality of the value against the enum instance.
     *
     * @param mixed $enumValue
     * @return void
     */
    public function is($enumValue)
    {
        if (!static::hasValue($enumValue)) {
            throw new InvalidEnumMemberException($enumValue, $this);
        }

        return $this->value === $enumValue;
    }
}
