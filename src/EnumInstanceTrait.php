<?php

namespace BenSampo\Enum;

trait EnumInstanceTrait
{

    /**
     * The value of one the enum constants.
     *
     * @var mixed
     */
    protected $value;

    /**
     * The constructor needs to be hidden so that an enum
     * instance can't created with invalid values
     *
     * @param mixed $value
     */
    protected function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Checks the equality of the value against the enum instance.
     *
     * @param mixed $value
     * @return void
     */
    public function equals($value)
    {
        static::validateValue($value);
        return $this->value === $value;
    }

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


    /**
     * Validates that the given value exists in the constants
     * definition/list.
     *
     * @param mixed $value
     * @throws InvalidArgumentException
     */
    protected static function validateValue($value)
    {
        if (!static::hasValue($value)) {
            $values = implode(', ', static::getValues());
            throw new \InvalidArgumentException("Value {$value} doesn't exist in [{$values}]");
        }
    }
}
