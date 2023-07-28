<?php

use BenSampo\Enum\Tests\Enums\UserType;
/** @var UserType $userType */

[$userType->value => null];
[UserType::Administrator => null];
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;
/** @var UserType $userType */

[$userType->value => null];
[UserType::Administrator->value => null];
