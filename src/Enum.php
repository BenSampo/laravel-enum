<?php

namespace BenSampo\Enum;

use BenSampo\Enum\Contracts\EnumContract;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Exceptions\InvalidEnumMemberException;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use ReflectionClass;

abstract class Enum implements EnumContract
{
    use Macroable {
        // Because this class also defines a '__callStatic' method, a new name has to be given to the trait's '__callStatic' method.
        __callStatic as macroCallStatic;
    }

    /**
     * The key of one of the enum members.
     *
     * @var mixed
     */
    public $key;

    /**
     * The value of one of the enum members.
     *
     * @var mixed
     */
    public $value;

    /**
     * The description of one of the enum members.
     *
     * @var mixed
     */
    public $description;

    /**
     * Constants cache.
     *
     * @var array
     */
    protected static $constCacheArray = [];

    /**
     * Construct an Enum instance.
     *
     * @param  mixed  $enumValue
     * @return void
     */
    public function __construct($enumValue)
    {
        if (!static::hasValue($enumValue)) {
            throw new InvalidEnumMemberException($enumValue, $this);
        }

        $this->value = $enumValue;
        $this->key = static::getKey($enumValue);
        $this->description = static::getDescription($enumValue);
    }

    /**
     * Attempt to instantiate an enum by calling the enum key as a static method.
     *
     * This function defers to the macroable __callStatic function if a macro is found using the static method called.
     *
     * @param  string  $method
     * @param  mixed  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return static::macroCallStatic($method, $parameters);
        }

        if (static::hasKey($method)) {
            $enumValue = static::getValue($method);
            return new static($enumValue);
        }

        throw new \BadMethodCallException("Cannot create an enum instance for $method. The enum value $method does not exist.");
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Checks if this instance is equal to the given enum instance or value.
     *
     * @param  static|mixed  $enumValue
     * @return bool
     */
    public function is($enumValue): bool
    {
        if ($enumValue instanceof static) {
            return $this->value === $enumValue->value;
        }

        return $this->value === $enumValue;
    }

    /**
     * Checks if this instance is not equal to the given enum instance or value.
     *
     * @param  static|mixed  $enumValue
     * @return bool
     */
    public function isNot($enumValue): bool
    {
        return ! $this->is($enumValue);
    }

    /**
     * Checks if a matching enum instance or value is in the given array.
     *
     * @param  (mixed|static)[]  $values
     * @return bool
     */
    public function in(array $values): bool
    {
        foreach ($values as $value) {
            if ($this->is($value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return a new Enum instance,
     *
     * @param  mixed  $enumValue
     * @return static
     */
    public static function getInstance($enumValue): self
    {
        if ($enumValue instanceof static) {
            return $enumValue;
        }

        return new static($enumValue);
    }

    /**
     * Return instances of all the contained values.
     *
     * @return static[]
     */
    public static function getInstances(): array
    {
        return array_map(
            function ($constantValue) {
                return new static($constantValue);
            },
            static::getConstants()
        );
    }

    /**
     * Attempt to instantiate a new Enum using the given key or value.
     *
     * @param  mixed  $enumKeyOrValue
     * @return static|null
     */
    public static function coerce($enumKeyOrValue): ?self
    {
        if ($enumKeyOrValue === null) {
            return null;
        }

        if (static::hasValue($enumKeyOrValue)) {
            return static::getInstance($enumKeyOrValue);
        }

        if (is_string($enumKeyOrValue) && static::hasKey($enumKeyOrValue)) {
            return static::$enumKeyOrValue();
        }
        
        return null;
    }

    /**
     * Get all of the constants defined on the class.
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
     * Get all of the enum keys.
     *
     * @return array
     */
    public static function getKeys(): array
    {
        return array_keys(static::getConstants());
    }

    /**
     * Get all of the enum values.
     *
     * @return array
     */
    public static function getValues(): array
    {
        return array_values(static::getConstants());
    }

    /**
     * Get the key for a single enum value.
     *
     * @param  mixed  $value
     * @return string
     */
    public static function getKey($value): string
    {
        return array_search($value, static::getConstants(), true);
    }

    /**
     * Get the value for a single enum key
     *
     * @param  string  $key
     * @return mixed
     */
    public static function getValue(string $key)
    {
        return static::getConstants()[$key];
    }

    /**
     * Get the description for an enum value
     *
     * @param  mixed  $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return
            static::getLocalizedDescription($value) ??
            static::getFriendlyKeyName(static::getKey($value));
    }

    /**
     * Get the localized description of a value.
     *
     * This works only if localization is enabled
     * for the enum and if the key exists in the lang file.
     *
     * @param  mixed  $value
     * @return string|null
     */
    protected static function getLocalizedDescription($value): ?string
    {
        if (static::isLocalizable()) {
            $localizedStringKey = static::getLocalizationKey() . '.' . $value;

            if (Lang::has($localizedStringKey)) {
                return __($localizedStringKey);
            }
        }

        return null;
    }

    /**
     * Get a random key from the enum.
     *
     * @return string
     */
    public static function getRandomKey(): string
    {
        $keys = static::getKeys();

        return $keys[array_rand($keys)];
    }

    /**
     * Get a random value from the enum.
     *
     * @return mixed
     */
    public static function getRandomValue()
    {
        $values = static::getValues();

        return $values[array_rand($values)];
    }

    /**
     * Get a random instance of the enum.
     *
     * @return static
     */
    public static function getRandomInstance(): self
    {
        return new static(static::getRandomValue());
    }

    /**
     * Return the enum as an array.
     *
     * [string $key => mixed $value]
     *
     * @return array
     */
    public static function toArray(): array
    {
        return static::getConstants();
    }

    /**
     * Get the enum as an array formatted for a select.
     *
     * [mixed $value => string description]
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
     * Check that the enum contains a specific key.
     *
     * @param  string  $key
     * @return bool
     */
    public static function hasKey(string $key): bool
    {
        return in_array($key, static::getKeys(), true);
    }

    /**
     * Check that the enum contains a specific value
     *
     * @param  mixed  $value
     * @param  bool  $strict (Optional, defaults to True)
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
     * Transform the key name into a friendly, formatted version.
     *
     * @param  string  $key
     * @return string
     */
    protected static function getFriendlyKeyName(string $key): string
    {
        if (ctype_upper(str_replace('_', '', $key))) {
            $key = strtolower($key);
        }

        return ucfirst(str_replace('_', ' ', Str::snake($key)));
    }

    /**
     * Check that the enum implements the LocalizedEnum interface.
     *
     * @return bool
     */
    protected static function isLocalizable(): bool
    {
        return isset(class_implements(static::class)[LocalizedEnum::class]);
    }

    /**
     * Get the default localization key.
     *
     * @return string
     */
    public static function getLocalizationKey(): string
    {
        return 'enums.' . static::class;
    }
}
