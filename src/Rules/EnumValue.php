<?php

namespace BenSampo\Enum\Rules;

use BenSampo\Enum\FlaggedEnum;
use Illuminate\Contracts\Validation\Rule;

class EnumValue implements Rule
{
    /**
     * The name of the rule.
     */
    protected $rule = 'enum_value';

    /**
     * @var string|\BenSampo\Enum\Enum
     */
    protected $enumClass;

    /**
     * @var bool
     */
    protected $strict;

    /**
     * Create a new rule instance.
     *
     * @param  string  $enumClass
     * @param  bool  $strict
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(string $enumClass, bool $strict = true)
    {
        $this->enumClass = $enumClass;
        $this->strict = $strict;

        if (! class_exists($this->enumClass)) {
            throw new \InvalidArgumentException("Cannot validate against the enum, the class {$this->enumClass} doesn't exist.");
        }
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (is_subclass_of($this->enumClass, FlaggedEnum::class) && (is_integer($value) || ctype_digit($value))) {
            // Unset all possible flag values
            foreach ($this->enumClass::getValues() as $enumValue) {
                $value &= ~$enumValue;
            }
            // All bits should be unset
            return $value === 0;
        }
        return $this->enumClass::hasValue($value, $this->strict);
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return trans()->has('validation.enum_value')
            ? __('validation.enum_value')
            : __('laravelEnum::messages.enum_value');
    }

    /**
     * Convert the rule to a validation string.
     *
     * @return string
     *
     * @see \Illuminate\Validation\ValidationRuleParser::parseParameters
     */
    public function __toString()
    {
        $strict = $this->strict ? 'true' : 'false';

        return "{$this->rule}:{$this->enumClass},{$strict}";
    }
}
