<?php declare(strict_types=1);

use BenSampo\Enum\Tests\Enums\UserTypeLocalized;

return [
    UserTypeLocalized::class => [
        UserTypeLocalized::Administrator => 'Administrator',
        UserTypeLocalized::SuperAdministrator => 'Super administrator',
    ],
];
