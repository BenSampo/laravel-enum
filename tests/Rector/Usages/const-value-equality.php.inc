<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */

$foo?->var === UserType::Administrator;
UserType::Subscriber === UserType::Administrator;
2 != UserType::Subscriber;
UserType::Subscriber === 2;
$userType === UserType::Subscriber;
$userType === $mixed;
UserType::Subscriber != $userType;

-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */

$foo?->var === UserType::Administrator->value;
UserType::Subscriber === UserType::Administrator;
2 != UserType::Subscriber->value;
UserType::Subscriber->value === 2;
$userType === UserType::Subscriber;
$userType === $mixed;
UserType::Subscriber != $userType;
