<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */
$userType->is(UserType::Administrator);
$userType?->is(UserType::Administrator);
$userType->is($userType);
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */
$userType === UserType::Administrator;
$userType === UserType::Administrator;
$userType === $userType;
