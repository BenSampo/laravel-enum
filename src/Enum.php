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
     * @param int|string $value
     * @return int|string
     */
    public static function getKey($value): string
    {
        return array_search($value, self::getConstants(), true);
    }

    /**
     * Get the value for a single enum key
     *
     * @param string $key
     * @return int|string
     */
    public static function getValue(string $key)
    {
        return self::getConstants()[$key];
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
            self::getLocalizedDescription($value) ??
            self::getFriendlyKeyName(self::getKey($value));
    }

    /**
     * Get the localized description if localization is enabled 
     * for the enum and if they key exists in the lang file
     *
     * @param int|string $value
     * @return string
     */
    private static function getLocalizedDescription($value): ?string
    {
        if (self::isLocalizable())
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
     * @return int|string
     */
    public static function getRandomValue()
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

    /**
     * Check that the enum contains a specific key
     *
     * @param string $key
     * @return bool
     */
    public static function hasKey(string $key): bool
    {
        $validKeys = array_map('strtolower', self::getKeys());
        $normalizedKey = strtolower($key);

        return in_array($normalizedKey, $validKeys, true);
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
        $validValues = self::getValues();

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
    private static function getFriendlyKeyName(string $key): string
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
    private static function isLocalizable()
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
