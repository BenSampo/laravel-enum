<?php

namespace BenSampo\Enum\Exceptions;

use Exception;
use BenSampo\Enum\Enum;

class InvalidEnumMemberException extends Exception
{
    /**
     * Create an InvalidEnumMemberException.
     *
     * @param  mixed  $invalidValue
     * @param  \BenSampo\Enum\Enum  $enum
     * @return void
     */
    public function __construct($invalidValue, Enum $enum)
    {
        $invalidValueType = gettype($invalidValue);
        $enumValues = implode(', ', $enum::getValues());

        parent::__construct("Value ({$invalidValueType}) {$invalidValue} doesn't exist in [{$enumValues}]");
    }
}
