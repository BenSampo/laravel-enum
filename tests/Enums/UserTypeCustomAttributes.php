<?php

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

final class UserTypeCustomAttributes extends Enum implements LocalizedEnum
{
    const Moderator = 0;
    const Administrator = 1;
    const SuperAdministrator = 2;

    /**
     * This method available via $user->type->title
     * ---------- OR ----------
     * Statically available via UserTypeCustomAttributes::titles();
     * 
     * @return string
     */
    public function getTitleAttribute(int $value): string
    {
        return match($value) {
            self::Moderator => __('Moderator'),
            self::Administrator => __('Administrator'),
            self::SuperAdministrator => __('Super Administrator'),
        };
    }
}
