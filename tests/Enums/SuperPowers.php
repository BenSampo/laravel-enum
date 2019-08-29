<?php

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\FlaggedEnum;

final class SuperPowers extends FlaggedEnum
{
    const Flight = 1 << 1;
    const Invisibility = 1 << 2;
    const LaserVision = 1 << 3;
    const Strength = 1 << 4;
    const Teleportation = 1 << 5;
    const Immortality = 1 << 6;
    const TimeTravel = 1 << 7;

    const Superman = self::Flight | self::Strength | self::LaserVision;
}
