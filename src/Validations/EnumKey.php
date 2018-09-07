<?php

namespace BenSampo\Enum\Validations;


class EnumKey
{
    /**
     * Default error message.
     *
     * @var string
     */
    public static $errorMessage = 'The key you have entered is invalid.';

    /**
     * Validates whether given value belongs to enum.
     *
     * @param string                           $attribute
     * @param mixed                            $value
     * @param array                            $parameters
     * @param \Illuminate\Validation\Validator $validator
     * @return bool
     */
    public function validate($attribute, $value, $parameters, $validator)
    {
        $enum = array_get($parameters, 0, null);

        $enumKeyRule = new \BenSampo\Enum\Rules\EnumKey($enum);

        return $enumKeyRule->passes($attribute, $value);
    }
}