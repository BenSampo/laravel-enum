<?php

use BenSampo\Enum\Tests\Enums\UserType;

UserType::getKeys();
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

array_map(static fn(UserType $userType): string => $userType->name, UserType::cases());
