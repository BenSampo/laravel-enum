<?php

namespace BenSampo\Enum\Validations;

class EnumValue
{
    /**
     * Default error message.
     *
     * @var string
     */
    public static $errorMessage = 'The value you have entered is invalid.';

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

        $strict = array_get($parameters, 1, true);
        $strict = (boolean)json_decode(strtolower($strict));

        $enumValueRule = new \BenSampo\Enum\Rules\EnumValue($enum, $strict);

        return $enumValueRule->passes($attribute, $value);
    }
}