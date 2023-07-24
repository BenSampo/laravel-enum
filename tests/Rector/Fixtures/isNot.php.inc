<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */
$userType->isNot(UserType::Administrator);
$userType?->isNot(UserType::Administrator);
$userType->isNot($userType);
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */
$userType !== UserType::Administrator;
$userType !== UserType::Administrator;
$userType !== $userType;
