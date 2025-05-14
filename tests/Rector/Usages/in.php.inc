<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */
$userType->in([UserType::Administrator, UserType::Subscriber(), null]);
$userType?->in([UserType::Administrator, $userType, null]);
/** @var iterable<UserType> $iterable */
$userType->in($iterable);
/** @var \Illuminate\Support\Collection $collection */
$userType->in($collection);
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */
in_array($userType, [UserType::Administrator, UserType::Subscriber, null]);
in_array($userType, [UserType::Administrator, $userType, null]);
/** @var iterable<UserType> $iterable */
in_array($userType, iterator_to_array($iterable));
/** @var \Illuminate\Support\Collection $collection */
$collection->contains($userType);
