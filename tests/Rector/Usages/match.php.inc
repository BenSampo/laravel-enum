<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */

match ($userType->value) {
    UserType::Administrator => 1,
    new UserType(UserType::Subscriber) => 2,
    default => 3,
};
match ($userType?->value) {
    UserType::Administrator => 1,
    null => 2,
    default => 3,
};
match ($userType->value) {
    UserType::Administrator => 0,
    1 => 1,
    default => 2,
};
match ($mixed) {
    UserType::Administrator => 0,
    1 => 1,
    default => 2,
};
match ('foo') {
    UserType::Administrator, UserType::Subscriber => 1,
    $var => 2,
};
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */

match ($userType) {
    UserType::Administrator => 1,
    UserType::Subscriber => 2,
    default => 3,
};
match ($userType) {
    UserType::Administrator => 1,
    null => 2,
    default => 3,
};
match ($userType->value) {
    UserType::Administrator->value => 0,
    1 => 1,
    default => 2,
};
match ($mixed) {
    UserType::Administrator->value => 0,
    1 => 1,
    default => 2,
};
match ('foo') {
    UserType::Administrator->value, UserType::Subscriber->value => 1,
    $var => 2,
};
