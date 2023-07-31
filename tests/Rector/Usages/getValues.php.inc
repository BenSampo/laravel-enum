<?php

use BenSampo\Enum\Tests\Enums\UserType;

UserType::getValues();
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

array_map(static fn(UserType $userType) => $userType->value, UserType::cases());
