<?php

namespace BenSampo\Enum\Rules;

use Illuminate\Contracts\Validation\Rule;

class EnumValue implements Rule
{
    private $enumClass;

    /**
     * Create a new rule instance.
     */
    public function __construct(string $enum, bool $strict = true)
    {
        $this->enumClass = $enum;
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
        return app($this->enumClass)::hasValue($value, $this->strict);
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
