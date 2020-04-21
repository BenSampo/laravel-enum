<?php

namespace BenSampo\Enum\Exceptions;

use Exception;

class InvalidEnumKeyException extends Exception
{
    /**
     * Create an InvalidEnumKeyException.
     *
     * @param  mixed  $invalidKey
     * @param  string  $enumClass  A class-string of type \Bensampo\Enum\Enum
     * @return void
     */
    public function __construct($invalidKey, string $enumClass)
    {
        $invalidValueType = gettype($invalidKey);
        $enumKeys = implode(', ', $enumClass::getKeys());
        $enumClassName = class_basename($enumClass);

        parent::__construct("Cannot construct an instance of $enumClassName using the key ($invalidValueType) `$invalidKey`. Possible keys are [$enumKeys].");
    }
}
