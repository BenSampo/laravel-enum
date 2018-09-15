<?php

namespace BenSampo\Enum\Rules;

use Illuminate\Contracts\Validation\Rule;

class EnumValue implements Rule
{
    private $validValues;
    private $strict;

    /**
     * Create a new rule instance.
     */
    public function __construct(string $enum, bool $strict = true)
    {
        $this->validValues = app($enum)::getValues();
        $this->strict = $strict;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($this->strict) {
            return in_array($value, $this->validValues, true);
        }

        return in_array((string)$value, array_map('strval', $this->validValues), true);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('laravel-enum::validation.enum_value');
    }
}
