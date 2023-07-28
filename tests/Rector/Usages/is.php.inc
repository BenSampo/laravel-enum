<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */
/** @var int $int */
$userType->is(UserType::Administrator);
$userType?->is(UserType::Administrator);
$userType->is($userType);
$userType->is($int);
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
fn(mixed $value): bool => $userType === $value;