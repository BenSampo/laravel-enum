<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */

[UserType::Administrator => null];
match ($userType->value) {
    UserType::Administrator => null,
};
match ($userType->value) {
    1 => null,
};
match ('foo') {
    UserType::Administrator => null,
};
UserType::Administrator > 3;
2 * UserType::Subscriber;
someFunc() === UserType::Administrator;
$userType->is(UserType::Administrator);

-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */

[UserType::Administrator->value => null];
match ($userType) {
    UserType::Administrator => null,
};
match ($userType->value) {
    1 => null,
};
match ('foo') {
    UserType::Administrator->value => null,
};
UserType::Administrator->value > 3;
2 * UserType::Subscriber->value;
someFunc() === UserType::Administrator->value;
$userType === UserType::Administrator;
