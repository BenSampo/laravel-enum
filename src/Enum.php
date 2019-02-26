<?php

namespace BenSampo\Enum;

use ReflectionClass;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Facades\Lang;
use BenSampo\Enum\Contracts\LocalizedEnum;

abstract class Enum
{
    use Macroable;

    /**
     * The value of one the enum constants.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Constants cache
     *
     * @var array
     */
    protected static $constCacheArray = [];

    /**
     * The constructor needs to be hidden so that an enum
     * instance can't created with invalid values
     *
     * @param mixed $value
     */
    protected function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Checks the equality of the value against the enum instance.
     *
     * @param mixed $value
     * @return void
     */
    public function equals($value)
    {
        static::validateValue($value);
        return $this->value === $value;
    }

    /**
     * Get all of the constants on the class
     *
     * @return array
     */
    protected static function getConstants(): array
    {
        $calledClass = get_called_class();

        if (!array_key_exists($calledClass, static::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            static::$constCacheArray[$calledClass] = $reflect->getConstants();
        }

        return static::$constCacheArray[$calledClass];
    }

    /**
     * Get all of the enum keys
     *
     * @return array
     */
    public static function getKeys(): array
    {
        return array_keys(static::getConstants());
    }

    /**
     * Get all of the enum values
     *
     * @return array
     */
    public static function getValues(): array
    {
        return array_values(static::getConstants());
    }

    /**
     * Get the key for a single enum value
     *
     * @param int|string $value
     * @return int|string
     */
    public static function getKey($value): string
    {
        return array_search($value, static::getConstants(), true);
    }

    /**
     * Get the value for a single enum key
     *
     * @param string $key
     * @return int|string
     */
    public static function getValue(string $key)
    {
        return static::getConstants()[$key];
    }

    /**
     * Get the description for an enum value
     *
     * @param int|string $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return
            static::getLocalizedDescription($value) ??
            static::getFriendlyKeyName(static::getKey($value));
    }

    /**
     * Get the localized description if localization is enabled
     * for the enum and if they key exists in the lang file
     *
     * @param int|string $value
     * @return string
     */
    protected static function getLocalizedDescription($value): ?string
    {
        if (static::isLocalizable())
        {
            $localizedStringKey = static::getLocalizationKey() . '.' . $value;

            if (Lang::has($localizedStringKey))
            {
                return __($localizedStringKey);
            }
        }

        return null;
    }

    /**
     * Validates that the given value exists in the constants
     * definition/list.
     *
     * @param mixed $value
     * @throws InvalidArgumentException
     */
    protected static function validateValue($value)
    {
        if (!static::hasValue($value)) {
            $values = implode(', ', static::getValues());
            throw new \InvalidArgumentException("Value {$value} doesn't exist in [{$values}]");
        }
    }

    /**
     * Get a random key from the enum
     *
     * @return string
     */
    public static function getRandomKey(): string
    {
        $keys = static::getKeys();
        return $keys[array_rand($keys)];
    }

    /**
     * Get a random value from the enum
     *
     * @return int|string
     */
    public static function getRandomValue()
    {
        $values = static::getValues();
        return $values[array_rand($values)];
    }

    /**
     * Return the enum as an array
     *
     * @return array
     */
    public static function toArray(): array
    {
        return static::getConstants();
    }

    /**
     * Get the enum as an array formatted for a select.
     * value => description
     *
     * @return array
     */
    public static function toSelectArray(): array
    {
        $array = static::toArray();
        $selectArray = [];

        foreach ($array as $key => $value) {
            $selectArray[$value] = static::getDescription($value);
        }

        return $selectArray;
    }

    /**
     * Check that the enum contains a specific key
     *
     * @param string $key
     * @return bool
     */
    public static function hasKey(string $key): bool
    {
        return in_array($key, static::getKeys(), true);
    }

    /**
     * Check that the enum contains a specific value
     *
     * @param int|string $value
     * @param bool $strict (Optional, defaults to True)
     * @return bool
     */
    public static function hasValue($value, bool $strict = true): bool
    {
        $validValues = static::getValues();

        if ($strict) {
            return in_array($value, $validValues, true);
        }

        return in_array((string) $value, array_map('strval', $validValues), true);
    }

    /**
     * Transform the key name into a friendly, formatted version
     *
     * @param string $key
     * @return string
     */
    protected static function getFriendlyKeyName(string $key): string
    {
        if (ctype_upper(str_replace('_', '', $key))) {
            $key = strtolower($key);
        }

        return ucfirst(str_replace('_', ' ', snake_case($key)));
    }

    /**
     * Check that the enum implements the LocalizedEnum interface
     *
     * @return boolean
     */
    protected static function isLocalizable()
    {
        return isset(class_implements(static::class)[LocalizedEnum::class]);
    }

    /**
     * Get the default localization key
     *
     * @return string
     */
    public static function getLocalizationKey()
    {
        return 'enums.' . static::class;
    }
}
