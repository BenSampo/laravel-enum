<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */
$userType->in([UserType::Administrator, UserType::Subscriber(), null]);
$userType?->in([UserType::Administrator, $userType, null]);
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */
in_array($userType, [UserType::Administrator, UserType::Subscriber, null]);
in_array($userType, [UserType::Administrator, $userType, null]);
