<?php

namespace BenSampo\Enum;

use BenSampo\Enum\Exceptions\InvalidEnumMemberException;

abstract class FlaggedEnum extends Enum
{
    const None = 0;
    
    /**
     * Construct a FlaggedEnum instance.
     *
     * @param  int[]|Enum[]  $flags
     * @return void
     */
    public function __construct($flags)
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
     * Return a FlaggedEnum instance with defined flags.
     *
     * @param  int[]|Enum[]  $flags
     * @return void
     */
    public static function flags($flags): self
    {
        return static::getInstance($flags);
    }

    /**
     * Set the flags for the enum to the given array of flags.
     *
     * @param  int[]|Enum[]  $flags
     * @return self
     */
    public function setFlags(array $flags): self
    {
        $this->value = array_reduce($flags, function ($carry, $flag) {
            return $carry | static::getInstance($flag)->value;
        }, 0);

        return $this;
    }

    /**
     * Add the given flag to the enum.
     *
     * @param  int|Enum  $flag
     * @return self
     */
    public function addFlag($flag): self
    {
        $this->value |= static::getInstance($flag)->value;

        return $this;
    }

    /**
     * Add the given flags to the enum.
     *
     * @param  int[]|Enum[]  $flags
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
     * Remove the given flag from the enum.
     *
     * @param  int|Enum  $flag
     * @return self
     */
    public function removeFlag($flag): self
    {
        $this->value &= ~ static::getInstance($flag)->value;

        return $this;
    }

    /**
     * Remove the given flags from the enum.
     *
     * @param  int[]|Enum[]  $flags
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
     * Check if the enum has the specified flag.
     *
     * @param  int|Enum  $flag
     * @return bool
     */
    public function hasFlag($flag): bool
    {
        $flagValue = static::getInstance($flag)->value;

        if ($flagValue === 0) {
            return false;
        }
        
        return ($flagValue & $this->value) === $flagValue;
    }

    /**
     * Check if the enum has all of the specified flags.
     *
     * @param  int[]|Enum[]  $flags
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
     * @param  int|Enum  $flag
     * @return bool
     */
    public function notHasFlag($flag): bool
    {
        return ! $this->hasFlag($flag);
    }

    /**
     * Check if the enum doesn't have any of the specified flags.
     *
     * @param  int[]|Enum[]  $flags
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
     * @return Enum[]
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
