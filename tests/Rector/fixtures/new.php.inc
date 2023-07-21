<?php

use BenSampo\Enum\Tests\Enums\UserType;

new UserType('foo');
new UserType(UserType::Administrator);
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

\BenSampo\Enum\Tests\Enums\UserType::from('foo');
\BenSampo\Enum\Tests\Enums\UserType::Administrator;
