<?php

use BenSampo\Enum\Tests\Enums\UserType;

UserType::fromKey('foo');
UserType::fromKey(...);
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

array_filter(UserType::cases(), fn(UserType $userType): bool => $userType->name === 'foo')[0];
static fn(string $key): UserType => array_filter(UserType::cases(), fn(UserType $userType): bool => $userType->name === $key)[0];
