<?php declare(strict_types=1);

namespace BenSampo\Enum\Contracts;

interface LocalizedEnum
{
    /**
     * Get the default localization key.
     *
     * @return string
     */
    public static function getLocalizationKey();
}
