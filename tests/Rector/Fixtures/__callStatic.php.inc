<?php

use BenSampo\Enum\Tests\Enums\UserType;

UserType::Administrator();
UserType::NotAnEnumCase();
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

\BenSampo\Enum\Tests\Enums\UserType::Administrator;
UserType::NotAnEnumCase();
