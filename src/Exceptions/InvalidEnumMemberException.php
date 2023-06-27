<?php declare(strict_types=1);

namespace BenSampo\Enum\Exceptions;

class InvalidEnumMemberException extends \Exception
{
    /** @param  class-string<\BenSampo\Enum\Enum<mixed>>  $enum */
    public function __construct(mixed $invalidValue, string $enum)
    {
        $invalidValueType = gettype($invalidValue);
        $enumValues = implode(', ', $enum::getValues());
        $enumClassName = class_basename($enum);

        parent::__construct("Cannot construct an instance of {$enumClassName} using the value ({$invalidValueType}) `{$invalidValue}`. Possible values are [{$enumValues}].");
    }
}
