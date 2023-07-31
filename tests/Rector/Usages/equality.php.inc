<?php

use BenSampo\Enum\Tests\Enums\UserType;
use BenSampo\Enum\Tests\Enums\SuperPowers;

/** @var UserType $userType */
/** @var UserType|null $maybeUserType */

$foo?->var === UserType::Administrator;
UserType::Subscriber === UserType::Administrator;
UserType::Subscriber === SuperPowers::Flight;
2 != UserType::Subscriber;
UserType::Subscriber === 2;
$userType === UserType::Subscriber;
$userType->value === UserType::Subscriber;
UserType::Subscriber === $userType->value;
$maybeUserType?->value === UserType::Subscriber;
$userType === $mixed;
UserType::Subscriber != $userType;
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;
use BenSampo\Enum\Tests\Enums\SuperPowers;

/** @var UserType $userType */
/** @var UserType|null $maybeUserType */

$foo?->var === UserType::Administrator->value;
UserType::Subscriber === UserType::Administrator;
UserType::Subscriber->value === SuperPowers::Flight->value;
2 != UserType::Subscriber->value;
UserType::Subscriber->value === 2;
$userType === UserType::Subscriber;
$userType === UserType::Subscriber;
UserType::Subscriber === $userType;
$maybeUserType === UserType::Subscriber;
$userType === $mixed;
UserType::Subscriber != $userType;
