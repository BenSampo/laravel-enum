<?php declare(strict_types=1);

namespace BenSampo\Enum\Casts;

use BenSampo\Enum\Enum;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * @implements CastsAttributes<Enum|null, mixed>
 */
// @phpstan-ignore-next-line CastsAttributes is only sometimes generic
class EnumCast implements CastsAttributes
{
    public function __construct(
        protected string $enumClass
    ) {}

    /**
     * @template TValue
     *
     * @param  TValue $value
     * @param  array<string, mixed> $attributes
     *
     * @return Enum<TValue>|null
     */
    public function get($model, string $key, $value, array $attributes): ?Enum
    {
        return $this->castEnum($value);
    }

    /**
     * @param  array<string, mixed> $attributes
     *
     * @return array<string, mixed>
     */
    public function set($model, string $key, $value, array $attributes): array
    {
        $value = $this->castEnum($value);

        return [$key => $this->enumClass::serializeDatabase($value)];
    }

    /**
     * @template TValue
     *
     * @param  TValue $value
     *
     * @return Enum<TValue>|null
     */
    protected function castEnum(mixed $value): ?Enum
    {
        if ($value === null || $value instanceof $this->enumClass) {
            return $value;
        }

        $value = $this->getCastableValue($value);

        if ($value === null) {
            return null;
        }

        return $this->enumClass::fromValue($value);
    }

    protected function getCastableValue(mixed $value): mixed
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
