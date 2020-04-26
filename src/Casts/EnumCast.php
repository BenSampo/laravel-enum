<?php
declare(strict_types=1);

namespace BenSampo\Enum\Casts;

use BenSampo\Enum\Enum;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class EnumCast implements CastsAttributes
{
    /**@var string */
    private $enumClass;

    public function __construct(string $enumClass)
    {
        $this->enumClass = $enumClass;
    }

    /**
     * @inheritDoc
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return $this->castEnum($value);
    }

    /**
     * @inheritDoc
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if ($value === null) {
            return $value;
        }

        $enum = $this->enumClass;

        if (!$value instanceof $enum) {
            $value = $this->castEnum($value);
        }

        return [$key => $value->value];
    }

    /**
     * @param mixed $value
     *
     * @return Enum|null
     */
    protected function castEnum($value): ?Enum
    {
        $enum = $this->enumClass;

        if ($value === null || $value instanceof Enum) {
            return $value;
        }

        $value = $this->getCastableValue($value);

        return $enum::getInstance($value);
    }

    /**
     * Retrieve the value that can be casted into Enum
     *
     * @param mixed $value
     *
     * @return mixed
     */
    protected function getCastableValue($value)
    {
        // If the enum has overridden the `castNative` method, use it to get the cast value
        $value = $this->enumClass::castNative($value);

        // If the value exists in the enum (using strict type checking) return it
        if ($this->enumClass::hasValue($value)) {
            return $value;
        }

        // Try and find a value that can be type coerced into one that exists in the enum, returning if no matches
        if (!$this->enumClass::hasValue($value, false)) {
            return $value;
        }

        // Find the value in the enum that the incoming value can be coerced to
        foreach ($this->enumClass::getValues() as $enumValue) {
            if ($value == $enumValue) {
                return $enumValue;
            }
        }

        return $value;
    }
}
