<?php

namespace BenSampo\Enum\Rules;

use Illuminate\Contracts\Validation\Rule;

class EnumKey implements Rule
{
    private $enumClass;

    /**
     * Create a new rule instance.
     */
    public function __construct(string $enum)
    {
        $this->enumClass = $enum;
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
        return app($this->enumClass)::hasKey($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The key you have entered is invalid.';
    }
}
