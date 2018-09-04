<?php

namespace BenSampo\Enum;

use ReflectionClass;
use Illuminate\Support\Traits\Macroable;

abstract class Enum
{
    use Macroable;

    /**
     * Localization key in Language file
     *
     * @var string
     */
    protected static $localizationKey = '';

    /**
     * Constants cache
     *
     * @var array
     */
    private static $constCacheArray = [];

    /**
     * Get all of the constants on the class
     *
     * @return array
     */
    private static function getConstants(): array
    {
        $calledClass = get_called_class();

        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }

        return self::$constCacheArray[$calledClass];
    }

    /**
     * Get all of the enum keys
     *
     * @return array
     */
    public static function getKeys(): array
    {
        return array_keys(self::getConstants());
    }

    /**
     * Get all of the enum values
     *
     * @return array
     */
    public static function getValues(): array
    {
        return array_values(self::getConstants());
    }

    /**
     * Get the key for a single enum value
     *
     * @param mixed $value
     * @return string
     */
    public static function getKey($value): string
    {
        return array_search($value, self::getConstants(), true);
    }

    /**
     * Get the value for a single enum key
     *
     * @param string $key
     * @return void
     */
    public static function getValue(string $key)
    {
        return self::getConstants()[$key];
    }

    /**
     * Get the description for an enum value
     *
     * @param mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        $localizedStringKey = static::$localizationKey . '.' . $value;

        if (!empty(static::$localizationKey) && strpos(__($localizedStringKey), static::$localizationKey) !== 0) {
            return __($localizedStringKey);
        }

        $key = self::getKey($value);

        if (ctype_upper($key)) {
            $key = strtolower($key);
        }

        return ucfirst(str_replace('_', ' ', snake_case($key)));
    }

    /**
     * Get a random key from the enum
     *
     * @return string
     */
    public static function getRandomKey(): string
    {
        $keys = self::getKeys();
        return $keys[array_rand($keys)];
    }

    /**
     * Get a random value from the enum
     *
     * @return string
     */
    public static function getRandomValue(): string
    {
        $values = self::getValues();
        return $values[array_rand($values)];
    }

    /**
     * Return the enum as an array
     *
     * @return array
     */
    public static function toArray(): array
    {
        return self::getConstants();
    }

    /**
     * Get the enum as an array formatted for a select.
     * value => description
     *
     * @return array
     */
    public static function toSelectArray(): array
    {
        $array = self::toArray();
        $selectArray = [];

        foreach ($array as $key => $value) {
            $selectArray[$value] = static::getDescription($value);
        }

        return $selectArray;
    }
}
