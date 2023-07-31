<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */
/** @var UserType|null $maybeUserType */

"{$userType}";
"{$maybeUserType}";
$userType . '';
$maybeUserType . '';
(string) $userType;
(string) $maybeUserType;
$userType->__toString();
$maybeUserType?->__toString();
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */
/** @var UserType|null $maybeUserType */

"{$userType->value}";
"{$maybeUserType?->value}";
$userType->value . '';
$maybeUserType?->value . '';
(string) $userType->value;
(string) $maybeUserType?->value;
(string) $userType->value;
(string) $maybeUserType?->value;
