<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */

match ($userType->value) {
    UserType::Administrator => 1,
    UserType::Subscriber => 2,
    default => 3,
};
match ($userType->value) {
    1 => 1,
    default => 2,
};
match ('foo') {
    UserType::Administrator, UserType::Subscriber => null,
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
match ($userType->value) {
    1 => 1,
    default => 2,
};
match ('foo') {
    UserType::Administrator->value, UserType::Subscriber->value => null,
};
