<?php

namespace BenSampo\Enum\Rules;

use Illuminate\Contracts\Validation\Rule;

class Enum implements Rule
{
    /**
     * The name of the rule.
     */
    protected $rule = 'enum';

    /**
     * @var string|\BenSampo\Enum\Enum
     */
    protected $enumClass;

    /**
     * @var string
     */
    protected $message;

    /**
     * Create a new rule instance.
     *
     * @param  string  $enum
     * @param  string  $message
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(string $enum, string $message = null)
    {
        $this->enumClass = $enum;
        $this->message = $message;

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
        return $value instanceof $this->enumClass;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return $this->message ?: __('laravelEnum::messages.enum');
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
        return "{$this->rule}:{$this->enumClass}";
    }
}
