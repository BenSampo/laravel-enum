<?php

use BenSampo\Enum\Tests\Enums\UserType;
/** @var UserType $userType */

['foo' => $userType];
[$userType];
['foo' => UserType::Administrator];
[UserType::Administrator];
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;
/** @var UserType $userType */

['foo' => $userType];
[$userType];
['foo' => UserType::Administrator->value];
[UserType::Administrator->value];
