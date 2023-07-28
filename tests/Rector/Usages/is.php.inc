<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */
/** @var int $int */
$userType->is(UserType::Administrator);
$userType?->is(UserType::Administrator);
$userType->is($userType);
$userType->is($int);
(new UserType(UserType::Administrator))->is($userType);
(new UserType(UserType::Administrator))->is(...);
$userType->is(...);
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */
/** @var int $int */
$userType === UserType::Administrator;
$userType === UserType::Administrator;
$userType === $userType;
$userType->value === $int;
UserType::Administrator === $userType;
fn(mixed $value): bool => UserType::Administrator === $value;
fn(mixed $value): bool => $userType === $value;
