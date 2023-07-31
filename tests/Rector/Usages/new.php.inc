<?php

use BenSampo\Enum\Tests\Enums\UserType;

new UserType('foo');
new UserType(UserType::Administrator);
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

UserType::from('foo');
UserType::Administrator;
