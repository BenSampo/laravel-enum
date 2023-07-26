<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */

match ($userType->value) {
    UserType::Administrator => null,
};
match ($userType->value) {
    1 => null,
};
match ('foo') {
    UserType::Administrator => null,
};

-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */

match ($userType) {
    UserType::Administrator => null,
};
match ($userType->value) {
    1 => null,
};
match ('foo') {
    UserType::Administrator->value => null,
};
