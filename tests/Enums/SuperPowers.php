<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\FlaggedEnum;

final class SuperPowers extends FlaggedEnum
{
    public const Flight = 1 << 0;
    public const Invisibility = 1 << 1;
    public const LaserVision = 1 << 2;
    public const Strength = 1 << 3;
    public const Teleportation = 1 << 4;
    public const Immortality = 1 << 5;
    public const TimeTravel = 1 << 6;

    public const Superman = self::Flight | self::Strength | self::LaserVision;
}
