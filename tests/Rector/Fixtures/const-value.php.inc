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
someFunc() === UserType::Administrator->value;
$userType === UserType::Administrator;
