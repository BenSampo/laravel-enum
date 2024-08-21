<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */
$userType->notIn([UserType::Administrator]);
$userType?->notIn([UserType::Administrator, $userType]);
/** @var iterable<UserType> $userTypes */
$userType->notIn($userTypes);
/** @var \Illuminate\Support\Collection $collection */
$userType->notIn($collection);
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */
!in_array($userType, [UserType::Administrator]);
!in_array($userType, [UserType::Administrator, $userType]);
/** @var iterable<UserType> $userTypes */
!in_array($userType, iterator_to_array($userTypes));
/** @var \Illuminate\Support\Collection $collection */
$collection->doesntContain($userType);
