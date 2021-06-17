<?php

namespace BenSampo\Enum;

use ReflectionClass;
use JsonSerializable;
use Illuminate\Support\Str;
use BenSampo\Enum\Casts\EnumCast;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Traits\Macroable;
use BenSampo\Enum\Contracts\EnumContract;
use BenSampo\Enum\Contracts\LocalizedEnum;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Database\Eloquent\Castable;
use BenSampo\Enum\Exceptions\InvalidEnumKeyException;
use BenSampo\Enum\Exceptions\InvalidEnumMemberException;

abstract class Enum implements EnumContract, Castable, Arrayable, JsonSerializable
{
    use Macroable {
        __callStatic as macroCallStatic;
        __call as macroCall;
    }

    /**
     * The value of one of the enum members.
     *
     * @var mixed
     */
    public $value;

    /**
     * The key of one of the enum members.
     *
     * @var mixed
     */
    public $key;

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
     *
     * @throws \BenSampo\Enum\Exceptions\InvalidEnumMemberException
     */
    public function __construct($enumValue)
    {
        if (! static::hasValue($enumValue)) {
            throw new InvalidEnumMemberException($enumValue, $this);
        }

        $this->value = $enumValue;
        $this->key = static::getKey($enumValue);
        $this->description = static::getDescription($enumValue);
    }

    /**
     * Make a new instance from an enum value.
     *
     * @param  mixed  $enumValue
     * @return static
     */
    public static function fromValue($enumValue): self
    {
        if ($enumValue instanceof static) {
            return $enumValue;
        }

        return new static($enumValue);
    }

    /**
     * Alias for fromValue();.
     *
     * @param  mixed  $enumValue
     * @return static
     *
     * @deprecated in favour of fromValue(), might be removed in a major version
     */
    public static function getInstance($enumValue): self
    {
        return static::fromValue($enumValue);
    }

    /**
     * Make an enum instance from a given key.
     *
     * @param  string  $key
     * @return static
     *
     * @throws \BenSampo\Enum\Exceptions\InvalidEnumKeyException
     */
    public static function fromKey(string $key): self
    {
        if (static::hasKey($key)) {
            $enumValue = static::getValue($key);

            return new static($enumValue);
        }

        throw new InvalidEnumKeyException($key, static::class);
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

        return static::fromKey($method);
    }

    /**
     * Delegate magic method calls to macro's or the static call.
     *
     * While it is not typical to use the magic instantiation dynamically, it may happen
     * incidentally when calling the instantiation in an instance method of itself.
     * Even when using the `static::KEY()` syntax, PHP still interprets this is a call to
     * an instance method when it happens inside of an instance method of the same class.
     *
     * @param  string  $method
     * @param  mixed  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return self::__callStatic($method, $parameters);
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
    public function in(iterable $values): bool
    {
        foreach ($values as $value) {
            if ($this->is($value)) {
                return true;
            }
        }

        return false;
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
    public static function coerce($enumKeyOrValue): ?Enum
    {
        if ($enumKeyOrValue === null) {
            return null;
        }

        if ($enumKeyOrValue instanceof static) {
            return $enumKeyOrValue;
        }

        if (static::hasValue($enumKeyOrValue)) {
            return static::fromValue($enumKeyOrValue);
        }

        if (is_string($enumKeyOrValue) && static::hasKey($enumKeyOrValue)) {
            $enumValue = static::getValue($enumKeyOrValue);

            return new static($enumValue);
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

        if (! array_key_exists($calledClass, static::$constCacheArray)) {
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
     * Get the value for a single enum key.
     *
     * @param  string  $key
     * @return mixed
     */
    public static function getValue(string $key)
    {
        return static::getConstants()[$key];
    }

    /**
     * Get the description for an enum value.
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
    public static function asArray()
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
    public static function asSelectArray(): array
    {
        $array = static::asArray();
        $selectArray = [];

        foreach ($array as $key => $value) {
            $selectArray[$value] = static::getDescription($value);
        }

        return $selectArray;
    }

    /**
     * @deprecated use self::asSelectArray()
     *
     * @return array
     */
    public static function toSelectArray(): array
    {
        return self::asSelectArray();
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
     * Check that the enum contains a specific value.
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
        if (ctype_upper(preg_replace('/[^a-zA-Z]/', '', $key))) {
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

    /**
     * Cast values loaded from the database before constructing an enum from them.
     *
     * You may need to overwrite this when using string values that are returned
     * from a raw database query or a similar data source.
     *
     * @param  mixed  $value  A raw value that may have any native type
     * @return mixed  The value cast into the type this enum expects
     */
    public static function parseDatabase($value)
    {
        return $value;
    }

    /**
     * Transform value from the enum instance before it's persisted to the database.
     *
     * You may need to overwrite this when using a database that expects a different
     * type to that used internally in your enum.
     *
     * @param  mixed  $value  A raw value that may have any native type
     * @return mixed  The value cast into the type this database expects
     */
    public static function serializeDatabase($value)
    {
        if ($value instanceof self) {
            return $value->value;
        }

        return $value;
    }

    /**
     * Get the name of the caster class to use when casting from / to this cast target.
     *
     * @param  array  $arguments
     * @return string
     * @return string|\Illuminate\Contracts\Database\Eloquent\CastsAttributes|\Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes
     */
    public static function castUsing(array $arguments)
    {
        return new EnumCast(static::class);
    }

    /**
     * Transform the enum instance when it's converted to an array.
     *
     * @return string
     */
    public function toArray()
    {
        return $this->value;
    }

    /**
     * Transform the enum when it's passed through json_encode.
     *
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->value;
    }
}
