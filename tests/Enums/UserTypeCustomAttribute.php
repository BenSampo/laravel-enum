<?php

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Enum;

final class UserTypeCustomAttribute extends Enum
{
    const Administrator = 0;
    const Moderator = 1;
    const Subscriber = 2;
    const SuperAdministrator = 3;

    public function title(): string
    {
        return match($this->value) {
            static::Administrator => __('Administrator'),
            static::Moderator => __('Moderator'),
            static::Subscriber => __('Subscriber'),
            static::SuperAdministrator => __('Super Administrator'),
        };
    }

    public function tagColor(): string
    {
        return match($this->value) {
            static::Administrator => 'green',
            static::Moderator => 'orange',
            static::Subscriber => 'black',
            static::SuperAdministrator => 'red',
        };
    }
}
