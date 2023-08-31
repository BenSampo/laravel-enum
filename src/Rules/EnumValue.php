<?php declare(strict_types=1);

namespace BenSampo\Enum\Rules;

use BenSampo\Enum\FlaggedEnum;
use Illuminate\Contracts\Validation\Rule;

class EnumValue implements Rule
{
    /** The name of the rule. */
    protected string $rule = 'enum_value';

    /** @throws \InvalidArgumentException */
    public function __construct(
        protected string $enumClass,
        protected bool $strict = true
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
        if (is_subclass_of($this->enumClass, FlaggedEnum::class) && (is_integer($value) || ctype_digit($value))) {
            // Unset all possible flag values
            foreach ($this->enumClass::getValues() as $enumValue) {
                assert(is_int($enumValue), 'Flagged enum values must be int');
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
     * @return string|array<string>
     */
    public function message(): string|array
    {
        return trans()->has('validation.enum_value')
            ? __('validation.enum_value')
            : __('laravelEnum::messages.enum_value');
    }

    /**
     * Convert the rule to a validation string.
     *
     * @see \Illuminate\Validation\ValidationRuleParser::parseParameters
     */
    public function __toString(): string
    {
        $strict = $this->strict ? 'true' : 'false';

        return "{$this->rule}:{$this->enumClass},{$strict}";
    }
}
