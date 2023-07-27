<?php

use BenSampo\Enum\Tests\Enums\UserType;

fn () => UserType::Administrator;
function () {
    return UserType::Administrator;
};

-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

fn () => UserType::Administrator->value;
function () {
    return UserType::Administrator->value;
};
