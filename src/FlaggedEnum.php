<?php declare(strict_types=1);

namespace BenSampo\Enum;

use BenSampo\Enum\Exceptions\InvalidEnumMemberException;

/**
 * @method static static None()
 *
 * @extends Enum<int>
 */
abstract class FlaggedEnum extends Enum
{
    /**
     * The value of one of the enum members.
     *
     * @var int
     */
    public $value;

    /** Unset, do not access. */
    public string $key;

    /** Unset, do not access. */
    public string $description;

    public const None = 0;

    /**
     * Construct a FlaggedEnum instance.
     *
     * @param  int|self|array<int|self>  $flags
     */
    public function __construct(mixed $flags = [])
    {
        unset($this->key, $this->description);

        if (is_array($flags)) {
            $this->setFlags($flags);
        } else {
            try {
                parent::__construct($flags);
            } catch (InvalidEnumMemberException) {
                $this->value = $flags;
            }
        }
    }

    /** @param  int|static|array<int|static> $enumValue */
    public static function fromValue(mixed $enumValue): static
    {
        return parent::fromValue($enumValue);
    }

    /** Attempt to instantiate a new Enum using the given key or value. */
    public static function coerce(mixed $enumKeyOrValue): ?static
    {
        if (is_integer($enumKeyOrValue)) {
            return static::fromValue($enumKeyOrValue);
        }

        return parent::coerce($enumKeyOrValue);
    }

    /**
     * Return a FlaggedEnum instance with defined flags.
     *
     * @param  array<int|static>  $flags
     */
    public static function flags(array $flags): static
    {
        return static::fromValue($flags);
    }

    /**
     * Set the flags for the enum to the given array of flags.
     *
     * @param  array<int|static>  $flags
     */
    public function setFlags(array $flags): static
    {
        $this->value = array_reduce(
            $flags,
            static fn (int $carry, int|self $flag): int => $carry
                | ($flag instanceof self
                    ? $flag->value
                    : $flag),
            0
        );

        return $this;
    }

    /**
     * Add the given flag to the enum.
     *
     * @param  int|static $flag
     */
    public function addFlag(int|self $flag): static
    {
        $this->value |= ($flag instanceof self
            ? $flag->value
            : $flag);

        return $this;
    }

    /**
     * Add the given flags to the enum.
     *
     * @param  array<int|static>  $flags
     */
    public function addFlags(array $flags): static
    {
        foreach ($flags as $flag) {
            $this->addFlag($flag);
        }

        return $this;
    }

    /** Add all flags to the enum. */
    public function addAllFlags(): static
    {
        return (new static())->addFlags(self::getValues());
    }

    /**
     * Remove the given flag from the enum.
     *
     * @param  int|static $flag
     */
    public function removeFlag(int|self $flag): static
    {
        $this->value &= ~($flag instanceof self
            ? $flag->value
            : $flag);

        return $this;
    }

    /**
     * Remove the given flags from the enum.
     *
     * @param  array<int|static>  $flags
     */
    public function removeFlags(array $flags): static
    {
        foreach ($flags as $flag) {
            $this->removeFlag($flag);
        }

        return $this;
    }

    /** Remove all flags from the enum. */
    public function removeAllFlags(): static
    {
        return static::None();
    }

    /**
     * Check if the enum has the specified flag.
     *
     * @param  int|static $flag
     */
    public function hasFlag(int|self $flag): bool
    {
        $flagValue = ($flag instanceof self
            ? $flag->value
            : $flag);

        if ($flagValue === 0) {
            return false;
        }

        return ($flagValue & $this->value) === $flagValue;
    }

    /**
     * Check if the enum has all specified flags.
     *
     * @param  array<int|static>  $flags
     */
    public function hasFlags(array $flags): bool
    {
        foreach ($flags as $flag) {
            if (! static::hasFlag($flag)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the enum does not have the specified flag.
     *
     * @param  int|static $flag
     */
    public function notHasFlag(int|Enum $flag): bool
    {
        return ! $this->hasFlag($flag);
    }

    /**
     * Check if the enum doesn't have any of the specified flags.
     *
     * @param  array<int|static>  $flags
     */
    public function notHasFlags(array $flags): bool
    {
        foreach ($flags as $flag) {
            if (! static::notHasFlag($flag)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return the flags as an array of instances.
     *
     * @return array<static>
     */
    public function getFlags(): array
    {
        $members = static::getInstances();
        $flags = [];

        foreach ($members as $member) {
            if ($this->hasFlag($member)) {
                $flags[] = $member;
            }
        }

        return $flags;
    }

    /** Check if there are multiple flags set on the enum. */
    public function hasMultipleFlags(): bool
    {
        return ($this->value & ($this->value - 1)) !== 0;
    }

    /** Get the bitmask for the enum. */
    public function getBitmask(): int
    {
        return (int) decbin($this->value);
    }
}
