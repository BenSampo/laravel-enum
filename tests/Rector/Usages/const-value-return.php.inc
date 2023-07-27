<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */

fn () => UserType::Administrator;
fn () => $userType;
function () { return UserType::Administrator; };
function () { return; };

-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */

fn () => UserType::Administrator->value;
fn () => $userType;
function () { return UserType::Administrator->value; };
function () { return; };
