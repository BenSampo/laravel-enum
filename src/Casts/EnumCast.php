<?php
declare(strict_types=1);

namespace BenSampo\Enum\Casts;

use BenSampo\Enum\Enum;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class EnumCast implements CastsAttributes
{
    /**@var string */
    private $enumClass;

    /** @var string|null */
    private $nativeType;

    public function __construct(string $enumClass, ?string $nativeType = null)
    {
        $this->enumClass = $enumClass;
        $this->nativeType = $nativeType;
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

        if ($this->nativeType !== null) {
            $value = $this->performNativeCast($value);
        }

        return $enum::getInstance($value);
    }

    protected function performNativeCast($value)
    {
        switch ($this->nativeType) {
            case 'int':
            case 'integer':
                return (int) $value;
            case 'real':
            case 'float':
            case 'double':
                return (float) $value;
            case 'string':
                return (string) $value;
            case 'bool':
            case 'boolean':
                return (bool) $value;
        }

        return $value;
    }
}
