<?php

namespace BenSampo\Enum\Traits;

use BenSampo\Enum\Enum;

trait CastsEnums
{
    /**
     * Get a plain attribute (not a relationship).
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttributeValue($key)
    {
        if ($this->hasEnumCast($key)) {
            return $this->castToEnum($key);
        }

        return parent::getAttributeValue($key);
    }

    /**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        if ($value !== null && $this->hasEnumCast($key)) {
            $enum = $this->enumCasts[$key];

            if ($value instanceOf $enum) {
                $this->attributes[$key] = $value->value;
            } else {
                $this->attributes[$key] = $enum::getInstance($value)->value;
            }

            return $this;
        }

        parent::setAttribute($key, $value);
    }

    /**
     * Determine whether an attribute should be cast to a enum.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasEnumCast($key)
    {
        return array_key_exists($key, $this->enumCasts);
    }

    /**
     * Casts the given key to an enum instance
     *
     * @param mixed $key
     * @return Enum|null
     */
    protected function castToEnum($key): ?Enum
    {
        $enum = $this->enumCasts[$key];
        $value = $this->getAttributeFromArray($key);

        if ($value === null || $value instanceOf Enum) {
            return $value;
        } else {
            return $enum::getInstance($value);
        }
    }
}
