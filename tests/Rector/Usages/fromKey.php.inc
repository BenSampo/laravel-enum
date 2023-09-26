<?php

use BenSampo\Enum\Tests\Enums\UserType;

UserType::fromKey('foo');
UserType::fromKey(...);
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

Illuminate\Support\Arr::first(array_filter(UserType::cases(), fn(UserType $userType): bool => $userType->name === 'foo'));
static fn(string $key): UserType => Illuminate\Support\Arr::first(array_filter(UserType::cases(), fn(UserType $userType): bool => $userType->name === $key));
