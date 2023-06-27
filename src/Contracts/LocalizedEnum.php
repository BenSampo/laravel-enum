<?php declare(strict_types=1);

namespace BenSampo\Enum\Contracts;

interface LocalizedEnum
{
    /** Get the default localization key. */
    public static function getLocalizationKey(): string;
}
