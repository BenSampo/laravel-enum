<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */
/** @var UserType|null $maybeUserType */

function notStrict(string|null $value) {}
notStrict($userType);
notStrict($maybeUserType);
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */
/** @var UserType|null $maybeUserType */

function notStrict(string|null $value) {}
notStrict($userType->value);
notStrict($maybeUserType?->value);
