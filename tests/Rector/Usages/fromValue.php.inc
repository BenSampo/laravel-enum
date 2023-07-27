<?php

use BenSampo\Enum\Tests\Enums\UserType;

UserType::fromValue('foo');
UserType::fromValue(UserType::Administrator);
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

\BenSampo\Enum\Tests\Enums\UserType::from('foo');
UserType::Administrator;
