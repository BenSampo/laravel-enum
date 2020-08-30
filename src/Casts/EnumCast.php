<?php
declare(strict_types=1);

namespace BenSampo\Enum\Casts;

use BenSampo\Enum\Enum;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class EnumCast implements CastsAttributes
{
    /** @var string */
    protected $enumClass;

    public function __construct(string $enumClass)
    {
        $this->enumClass = $enumClass;
    }

    public function get($model, string $key, $value, array $attributes)
    {
        return $this->castEnum($value);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        $value = $this->castEnum($value);

        return [$key => $this->enumClass::serializeDatabase($value)];
    }

    /**
     * @param  mixed  $value
     * @return \BenSampo\Enum\Enum|null
     */
    protected function castEnum($value): ?Enum
    {
        if ($value === null || $value instanceof $this->enumClass) {
            return $value;
        }

        $value = $this->getCastableValue($value);

        if ($value === null) {
            return null;
        }

        return $this->enumClass::getInstance($value);
    }

    /**
     * Retrieve the value that can be casted into Enum
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function getCastableValue($value)
    {
        // If the enum has overridden the `parseDatabase` method, use it to get the cast value
        $value = $this->enumClass::parseDatabase($value);

        if ($value === null) {
            return null;
        }

        // If the value exists in the enum (using strict type checking) return it
        if ($this->enumClass::hasValue($value)) {
            return $value;
        }

        // Find the value in the enum that the incoming value can be coerced to
        foreach ($this->enumClass::getValues() as $enumValue) {
            if ($value == $enumValue) {
                return $enumValue;
            }
        }

        // Fall back to trying to construct it directly (will result in an error since it doesn't exist)
        return $value;
    }
}
