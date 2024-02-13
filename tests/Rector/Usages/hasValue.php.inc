<?php

use BenSampo\Enum\Tests\Enums\UserType;

UserType::hasValue('foo');
UserType::hasValue('foo', false);
UserType::hasValue(UserType::Administrator);
UserType::hasValue(...);
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

UserType::tryFrom('foo') !== null;
UserType::tryFrom('foo') !== null;
true;
static fn(mixed $value): bool => UserType::tryFrom($value) !== null;
