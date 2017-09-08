<?php

namespace BenSampo\Enum\Rules;

use Illuminate\Contracts\Validation\Rule;

class EnumValue implements Rule
{
    private $validValues;

    /**
     * Create a new rule instance.
     */
    public function __construct(string $enum)
    {
        $this->validValues = resolve($enum)::getValues();
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
        if (in_array($value, $this->validValues)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The value you have entered is invalid.';
    }
}
