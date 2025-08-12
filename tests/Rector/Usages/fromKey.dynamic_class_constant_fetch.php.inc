<?php

use BenSampo\Enum\Tests\Enums\UserType;

UserType::fromKey('foo');
UserType::fromKey($key);
UserType::fromKey(...);
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

UserType::{'foo'};
UserType::{$key};
static fn(string $key): UserType => UserType::{$key};
