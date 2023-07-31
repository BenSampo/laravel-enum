<?php

use BenSampo\Enum\Tests\Enums\UserType;

UserType::Administrator();
UserType::NotAnEnumCase();
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

UserType::Administrator;
UserType::NotAnEnumCase();
