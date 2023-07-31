<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */

$maybe ? UserType::Administrator : $userType;
!$maybe ? $userType : UserType::Administrator;
$maybe ?: UserType::Administrator;
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */

$maybe ? UserType::Administrator->value : $userType;
!$maybe ? $userType : UserType::Administrator->value;
$maybe ?: UserType::Administrator->value;
