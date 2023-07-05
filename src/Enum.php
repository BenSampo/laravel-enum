<?php declare(strict_types=1);

namespace BenSampo\Enum;

use BenSampo\Enum\Attributes\Description;
use BenSampo\Enum\Casts\EnumCast;
use BenSampo\Enum\Contracts\EnumContract;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Exceptions\InvalidEnumKeyException;
use BenSampo\Enum\Exceptions\InvalidEnumMemberException;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;

/**
 * @template TValue
 *
 * @implements Arrayable<array-key, mixed>
 */
abstract class Enum implements EnumContract, Castable, Arrayable, \JsonSerializable
{
    use Macroable {
        __callStatic as macroCallStatic;
        __call as macroCall;
    }

    /**
     * The value of one of the enum members.
     *
     * @var TValue
     */
    public $value;

    /** The key of one of the enum members. */
    public string $key;

    /** The description of one of the enum members. */
    public string $description;

    /**
     * Caches reflections of enum subclasses.
     *
     * @var array<class-string<static>, \ReflectionClass<static>>
     */
    protected static array $reflectionCache = [];

    /**
     * Construct an Enum instance.
     *
     * @param  TValue  $enumValue
     *
     * @throws \BenSampo\Enum\Exceptions\InvalidEnumMemberException
     */
    public function __construct(mixed $enumValue)
    {
        if (! static::hasValue($enumValue)) {
            throw new InvalidEnumMemberException($enumValue, static::class);
        }

        $this->value = $enumValue;
        $this->key = static::getKey($enumValue);
        $this->description = static::getDescription($enumValue);
    }

    /**
     * Restores an enum instance exported by var_export().
     *
     * @param  array{value: TValue, key: string, description: string}  $enum
     */
    public static function __set_state(array $enum): static
    {
        return new static($enum['value']);
    }

    /**
     * Make a new instance from an enum value.
     *
     * @param  TValue  $enumValue
     */
    public static function fromValue(mixed $enumValue): static
    {
        if ($enumValue instanceof static) {
            return $enumValue;
        }

        return new static($enumValue);
    }

    /**
     * Returns a reflection of the enum subclass.
     *
     * @return \ReflectionClass<static>
     */
    protected static function getReflection(): \ReflectionClass
    {
        $class = static::class;

        return static::$reflectionCache[$class] ??= new \ReflectionClass($class);
    }

    /**
     * Make an enum instance from a given key.
     *
     * @throws \BenSampo\Enum\Exceptions\InvalidEnumKeyException
     */
    public static function fromKey(string $key): static
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
     * @param  array<mixed>  $parameters
     */
    public static function __callStatic(string $method, array $parameters): mixed
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
     * an instance method when it happens inside an instance method of the same class.
     *
     * @param  string  $method
     * @param  array<mixed>  $parameters
     */
    public function __call($method, $parameters): mixed
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return self::__callStatic($method, $parameters);
    }

    /** Checks if this instance is equal to the given enum instance or value. */
    public function is(mixed $enumValue): bool
    {
        if ($enumValue instanceof static) {
            return $this->value === $enumValue->value;
        }

        return $this->value === $enumValue;
    }

    /** Checks if this instance is not equal to the given enum instance or value. */
    public function isNot(mixed $enumValue): bool
    {
        return ! $this->is($enumValue);
    }

    /**
     * Checks if a matching enum instance or value is in the given values.
     *
     * @param  iterable<mixed>  $values
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
     * Checks if a matching enum instance or value is not in the given values.
     *
     * @param  iterable<mixed>  $values
     */
    public function notIn(iterable $values): bool
    {
        foreach ($values as $value) {
            if ($this->is($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return instances of all the contained values.
     *
     * @return array<string, static>
     */
    public static function getInstances(): array
    {
        return array_map(
            static fn (mixed $constantValue): self => new static($constantValue),
            static::getConstants()
        );
    }

    /** Attempt to instantiate a new Enum using the given key or value. */
    public static function coerce(mixed $enumKeyOrValue): ?static
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
     * Get all constants defined on the class.
     *
     * @return array<string, TValue>
     */
    protected static function getConstants(): array
    {
        return self::getReflection()->getConstants();
    }

    /**
     * Get all or a custom set of the enum keys.
     *
     * @param  TValue|array<TValue>|null  $values
     *
     * @return array<int, string>
     */
    public static function getKeys(mixed $values = null): array
    {
        if ($values === null) {
            return array_keys(static::getConstants());
        }

        return array_map(
            [static::class, 'getKey'],
            is_array($values) ? $values : func_get_args(),
        );
    }

    /**
     * Get all or a custom set of the enum values.
     *
     * @param  string|array<string>|null  $keys
     *
     * @return array<int, TValue>
     */
    public static function getValues(string|array $keys = null): array
    {
        if ($keys === null) {
            return array_values(static::getConstants());
        }

        return array_map(
            [static::class, 'getValue'],
            is_array($keys) ? $keys : func_get_args(),
        );
    }

    /**
     * Get the key for a single enum value.
     *
     * @param  TValue  $value
     */
    public static function getKey(mixed $value): string
    {
        return array_search($value, static::getConstants(), true)
            ?: throw new InvalidEnumMemberException($value, static::class);
    }

    /**
     * Get the value for a single enum key.
     *
     * @return TValue
     */
    public static function getValue(string $key): mixed
    {
        return static::getConstants()[$key];
    }

    /**
     * Get the description for an enum value.
     *
     * @param  TValue  $value
     */
    public static function getDescription(mixed $value): string
    {
        return
            static::getLocalizedDescription($value)
            ?? static::getAttributeDescription($value)
            ?? static::getFriendlyName(static::getKey($value));
    }

    /**
     * Get the localized description of a value.
     *
     * This works only if localization is enabled
     * for the enum and if the key exists in the lang file.
     *
     * @param  TValue  $value
     */
    protected static function getLocalizedDescription(mixed $value): ?string
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
     * Get the description of a value from its PHP attribute.
     *
     * @param  TValue  $value
     */
    protected static function getAttributeDescription(mixed $value): ?string
    {
        $reflection = self::getReflection();
        $constantName = static::getKey($value);
        $constReflection = $reflection->getReflectionConstant($constantName);
        if ($constReflection === false) {
            return null;
        }

        $descriptionAttributes = $constReflection->getAttributes(Description::class);

        return match (count($descriptionAttributes)) {
            0 => null,
            1 => $descriptionAttributes[0]->newInstance()->description,
            default => throw new \Exception('You cannot use more than 1 description attribute on ' . class_basename(static::class) . '::' . $constantName),
        };
    }

    /**
     * Get the description of the enum class.
     * Default to Enum class short name.
     */
    public static function getClassDescription(): string
    {
        return static::getClassAttributeDescription()
            ?? static::getFriendlyName(self::getReflection()->getShortName());
    }

    protected static function getClassAttributeDescription(): ?string
    {
        $reflection = self::getReflection();

        $descriptionAttributes = $reflection->getAttributes(Description::class);

        return match (count($descriptionAttributes)) {
            0 => null,
            1 => $descriptionAttributes[0]->newInstance()->description,
            default => throw new \Exception('You cannot use more than 1 description attribute on ' . class_basename(static::class))
        };
    }

    /** Get a random key from the enum. */
    public static function getRandomKey(): string
    {
        $keys = static::getKeys();

        return $keys[array_rand($keys)];
    }

    /**
     * Get a random value from the enum.
     *
     * @return TValue
     */
    public static function getRandomValue(): mixed
    {
        $values = static::getValues();

        return $values[array_rand($values)];
    }

    /** Get a random instance of the enum. */
    public static function getRandomInstance(): static
    {
        return new static(static::getRandomValue());
    }

    /**
     * Return the enum as an array.
     *
     * @return array<string, TValue>
     */
    public static function asArray(): array
    {
        return static::getConstants();
    }

    /**
     * Get the enum as an array formatted for a select.
     *
     * @return array<array-key, string>
     */
    public static function asSelectArray(): array
    {
        $array = static::asArray();
        $selectArray = [];

        foreach ($array as $value) {
            $selectArray[$value] = static::getDescription($value);
        }

        return $selectArray;
    }

    /**
     * @deprecated use self::asSelectArray()
     *
     * @return array<array-key, string>
     */
    public static function toSelectArray(): array
    {
        return self::asSelectArray();
    }

    /** Check that the enum contains a specific key. */
    public static function hasKey(string $key): bool
    {
        return in_array($key, static::getKeys(), true);
    }

    /** Check that the enum contains a specific value. */
    public static function hasValue(mixed $value, bool $strict = true): bool
    {
        $validValues = static::getValues();

        if ($strict) {
            return in_array($value, $validValues, true);
        }

        return in_array((string) $value, array_map('strval', $validValues), true);
    }

    /** Transform the name into a friendly, formatted version. */
    protected static function getFriendlyName(string $name): string
    {
        if (ctype_upper(preg_replace('/[^a-zA-Z]/', '', $name))) {
            $name = strtolower($name);
        }

        return ucfirst(str_replace('_', ' ', Str::snake($name)));
    }

    /** Check that the enum implements the LocalizedEnum interface. */
    protected static function isLocalizable(): bool
    {
        return isset(class_implements(static::class)[LocalizedEnum::class]);
    }

    /** Get the default localization key. */
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
     *
     * @return TValue|null  The value cast into the type this enum expects or null
     */
    public static function parseDatabase(mixed $value): mixed
    {
        return $value;
    }

    /**
     * Transform value from the enum instance before it's persisted to the database.
     *
     * You may need to overwrite this when using a database that expects a different
     * type to that used internally in your enum.
     *
     * @param  TValue  $value  A value of the type this enum expects
     *
     * @return mixed  The value cast into the type the database expects
     */
    public static function serializeDatabase(mixed $value): mixed
    {
        if ($value instanceof self) {
            return $value->value;
        }

        return $value;
    }

    /**
     * Get the name of the caster class to use when casting from / to this cast target.
     *
     * @param  array<mixed>  $arguments
     */
    public static function castUsing(array $arguments): EnumCast
    {
        return new EnumCast(static::class);
    }

    /**
     * Return a plain representation of the enum.
     *
     * This method is not meant to be called directly, but rather be called
     * by Laravel through a recursive serialization with @see \Illuminate\Contracts\Support\Arrayable.
     * Thus, it returns a value meant to be included in a plain array.
     *
     * @return TValue
     */
    public function toArray(): mixed
    {
        return $this->value;
    }

    /**
     * Return a JSON-serializable representation of the enum.
     *
     * @return TValue
     */
    public function jsonSerialize(): mixed
    {
        return $this->value;
    }

    /** Return a string representation of the enum. */
    public function __toString(): string
    {
        return (string) $this->value;
    }
}
