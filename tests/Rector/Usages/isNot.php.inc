<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */
$userType->isNot(UserType::Administrator);
$userType?->isNot(UserType::Administrator);
$userType->isNot($userType);
$userType->isNot(...);
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */
$userType !== UserType::Administrator;
$userType !== UserType::Administrator;
$userType !== $userType;
fn(mixed $value): bool => $userType !== $value;
