<?php

namespace BenSampo\Enum;

use ReflectionClass;

abstract class Enum
{
    private static $constCacheArray = [];

    private static function getConstants(): array
    {
        $calledClass = get_called_class();

        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }

        return self::$constCacheArray[$calledClass];
    }

    public static function getKeys(): array
    {
        return array_keys(self::getConstants());
    }

    public static function getValues(): array
    {
        return array_values(self::getConstants());
    }

    public static function getKey($value): string
    {
        return array_search($value, self::getConstants(), true);
    }

    public static function getValue(string $key)
    {
        return self::getConstants()[$key];
    }

    public static function getDescription($value): string
    {
        return self::getKey($value);
    }

    public static function getRandomKey(): string
    {
        $keys = self::getKeys();
        return $keys[array_rand($keys)];
    }

    public static function getRandomValue(): string
    {
        $values = self::getValues();
        return $values[array_rand($values)];
    }

    public static function toArray(): array
    {
        return self::getConstants();
    }
}
