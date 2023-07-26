<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */
$userType->in([UserType::Administrator]);
$userType?->in([UserType::Administrator, $userType]);
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */
in_array($userType, [UserType::Administrator]);
in_array($userType, [UserType::Administrator, $userType]);
