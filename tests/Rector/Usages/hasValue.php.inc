<?php

use BenSampo\Enum\Tests\Enums\UserType;
use BenSampo\Enum\Tests\Enums\StringValues;

/** @var int $int */
/** @var string $string */
/** @var bool $bool */
/** @var mixed $mixed */

UserType::hasValue($int);
UserType::hasValue($string);
UserType::hasValue($bool);
UserType::hasValue($mixed);
UserType::hasValue(UserType::Administrator);

StringValues::hasValue($int);
StringValues::hasValue($string);
StringValues::hasValue($bool);
StringValues::hasValue($mixed);
StringValues::hasValue(StringValues::Administrator);

UserType::hasValue(...);
StringValues::hasValue(...);
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;
use BenSampo\Enum\Tests\Enums\StringValues;

/** @var int $int */
/** @var string $string */
/** @var bool $bool */
/** @var mixed $mixed */

UserType::tryFrom($int) !== null;
false;
false;
is_int($mixed) && UserType::tryFrom($mixed) !== null;
true;

false;
StringValues::tryFrom($string) !== null;
false;
is_string($mixed) && StringValues::tryFrom($mixed) !== null;
true;

static fn(mixed $value): bool => is_int($value) && UserType::tryFrom($value) !== null;
static fn(mixed $value): bool => is_string($value) && StringValues::tryFrom($value) !== null;
