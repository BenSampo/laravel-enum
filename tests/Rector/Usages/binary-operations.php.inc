<?php

use BenSampo\Enum\Tests\Enums\UserType;
use BenSampo\Enum\Tests\Enums\StringValues;

/** @var UserType $userType */
/** @var StringValues|null $maybeStringValues */
/** @var string|null $maybeString */

UserType::Administrator > 3;
2 * UserType::Subscriber;
2 | UserType::Subscriber;
$userType + 5;

$param ?? UserType::Administrator;
$maybeString ?? StringValues::Administrator();
$maybeStringValues ?? 'fallback';
$maybeStringValues ?? StringValues::Administrator();
$userType->is($param ?? UserType::Administrator);
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;
use BenSampo\Enum\Tests\Enums\StringValues;

/** @var UserType $userType */
/** @var StringValues|null $maybeStringValues */
/** @var string|null $maybeString */

UserType::Administrator->value > 3;
2 * UserType::Subscriber->value;
2 | UserType::Subscriber->value;
$userType->value + 5;

$param ?? UserType::Administrator->value;
$maybeString ?? StringValues::Administrator->value;
$maybeStringValues->value ?? 'fallback';
$maybeStringValues ?? StringValues::Administrator;
$userType->value === ($param ?? UserType::Administrator->value);
