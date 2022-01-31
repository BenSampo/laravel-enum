<?php

namespace BenSampo\Enum;

use BenSampo\Enum\Exceptions\InvalidEnumMemberException;

abstract class FlaggedEnum extends Enum
{
    const None = 0;

    /**
     * Construct a FlaggedEnum instance.
     *
     * @param  int[]|\BenSampo\Enum\Enum[]  $flags
     * @return void
     */
    public function __construct($flags = [])
    {
        $this->key = null;
        $this->description = null;

        if (is_array($flags)) {
            $this->setFlags($flags);
        } else {
            try {
                parent::__construct($flags);
            } catch (InvalidEnumMemberException $exception) {
                $this->value = $flags;
            }
        }
    }

    /**
     * Attempt to instantiate a new Enum using the given key or value.
     *
     * @param  mixed  $enumKeyOrValue
     * @return static|null
     */
    public static function coerce($enumKeyOrValue): ?Enum
    {
        if (is_integer($enumKeyOrValue)) {
            return static::fromValue($enumKeyOrValue);
        }

        return parent::coerce($enumKeyOrValue);
    }

    /**
     * Return a FlaggedEnum instance with defined flags.
     *
     * @param  int[]|\BenSampo\Enum\Enum[]  $flags
     * @return self
     */
    public static function flags($flags): self
    {
        return static::fromValue($flags);
    }

    /**
     * Set the flags for the enum to the given array of flags.
     *
     * @param  int[]|\BenSampo\Enum\Enum[]  $flags
     * @return self
     */
    public function setFlags(array $flags): self
    {
        $this->value = array_reduce($flags, function ($carry, $flag) {
            return $carry | static::fromValue($flag)->value;
        }, 0);

        return $this;
    }

    /**
     * Add the given flag to the enum.
     *
     * @param  int|\BenSampo\Enum\Enum  $flag
     * @return self
     */
    public function addFlag($flag): self
    {
        $this->value |= static::fromValue($flag)->value;

        return $this;
    }

    /**
     * Add the given flags to the enum.
     *
     * @param  int[]|\BenSampo\Enum\Enum[]  $flags
     * @return self
     */
    public function addFlags(array $flags): self
    {
        array_map(function ($flag) {
            $this->addFlag($flag);
        }, $flags);

        return $this;
    }

    /**
     * Add all flags to the enum.
     *
     * @return self
     */
    public function addAllFlags(): self
    {
        return (new static)->addFlags(self::getValues());
    }

    /**
     * Remove the given flag from the enum.
     *
     * @param  int|\BenSampo\Enum\Enum  $flag
     * @return self
     */
    public function removeFlag($flag): self
    {
        $this->value &= ~static::fromValue($flag)->value;

        return $this;
    }

    /**
     * Remove the given flags from the enum.
     *
     * @param  int[]|\BenSampo\Enum\Enum[]  $flags
     * @return self
     */
    public function removeFlags(array $flags): self
    {
        array_map(function ($flag) {
            $this->removeFlag($flag);
        }, $flags);

        return $this;
    }

    /**
     * Remove all flags from the enum.
     *
     * @return self
     */
    public function removeAllFlags(): self
    {
        return static::None();
    }

    /**
     * Check if the enum has the specified flag.
     *
     * @param  int|\BenSampo\Enum\Enum  $flag
     * @return bool
     */
    public function hasFlag($flag): bool
    {
        $flagValue = static::fromValue($flag)->value;

        if ($flagValue === 0) {
            return false;
        }

        return ($flagValue & $this->value) === $flagValue;
    }

    /**
     * Check if the enum has all of the specified flags.
     *
     * @param  int[]|\BenSampo\Enum\Enum[]  $flags
     * @return bool
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
     * @param  int|\BenSampo\Enum\Enum  $flag
     * @return bool
     */
    public function notHasFlag($flag): bool
    {
        return ! $this->hasFlag($flag);
    }

    /**
     * Check if the enum doesn't have any of the specified flags.
     *
     * @param  int[]|\BenSampo\Enum\Enum[]  $flags
     * @return bool
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
     * @return \BenSampo\Enum\Enum[]
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

    /**
     * Check if there are multiple flags set on the enum.
     *
     * @return bool
     */
    public function hasMultipleFlags(): bool
    {
        return ($this->value & ($this->value - 1)) !== 0;
    }

    /**
     * Get the bitmask for the enum.
     *
     * @return int
     */
    public function getBitmask(): int
    {
        return (int) decbin($this->value);
    }
}
