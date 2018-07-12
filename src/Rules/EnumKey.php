<?php

namespace BenSampo\Enum\Rules;

use Illuminate\Contracts\Validation\Rule;

class EnumKey implements Rule
{
    private $validKeys;

    /**
     * Create a new rule instance.
     */
    public function __construct(string $enum)
    {
        $this->validKeys = array_map(function($key) {
            return strtolower($key);
        }, resolve($enum)::getKeys());
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
        $value = strtolower($value);

        return in_array($value, $this->validKeys, true);
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
