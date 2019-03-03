<?php

namespace BenSampo\Enum\Exceptions;

use Exception;
use BenSampo\Enum\Enum;

class InvalidEnumMemberException extends Exception
{
    public function __construct($invalidValue, Enum $enum) {
        $enumValues = implode(', ', $enum::getValues());
        parent::__construct("Value {$invalidValue} doesn't exist in [{$enumValues}]");
    }
}
