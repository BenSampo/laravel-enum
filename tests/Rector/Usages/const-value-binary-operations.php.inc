<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */

UserType::Administrator > 3;
2 * UserType::Subscriber;
2 | UserType::Subscriber->value;
$userType->is($param ?? UserType::Administrator);

-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */

UserType::Administrator->value > 3;
2 * UserType::Subscriber->value;
2 | UserType::Subscriber->value;
$userType->value === ($param ?? UserType::Administrator->value);
