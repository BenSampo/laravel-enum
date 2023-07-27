<?php

use BenSampo\Enum\Tests\Enums\UserType;

fn () => UserType::Administrator;
function () { return UserType::Administrator; };
function () { return; };

-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

fn () => UserType::Administrator->value;
function () { return UserType::Administrator->value; };
function () { return; };
