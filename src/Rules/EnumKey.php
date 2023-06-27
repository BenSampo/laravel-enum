<?php declare(strict_types=1);

namespace BenSampo\Enum\Rules;

use Illuminate\Contracts\Validation\Rule;

class EnumKey implements Rule
{
    /** The name of the rule. */
    protected string $rule = 'enum_key';

    public function __construct(
        /** @var class-string<\BenSampo\Enum\Enum<mixed>> */
        protected string $enumClass
    ) {
        if (! class_exists($this->enumClass)) {
            throw new \InvalidArgumentException("Cannot validate against the enum, the class {$this->enumClass} doesn't exist.");
        }
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     */
    public function passes($attribute, $value): bool
    {
        return is_string($value) && $this->enumClass::hasKey($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string|array<string>
     */
    public function message(): string|array
    {
        return trans()->has('validation.enum_key')
            ? __('validation.enum_key')
            : __('laravelEnum::messages.enum_key');
    }

    /**
     * Convert the rule to a validation string.
     *
     * @see \Illuminate\Validation\ValidationRuleParser::parseParameters
     */
    public function __toString(): string
    {
        return "{$this->rule}:{$this->enumClass}";
    }
}
