<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */

switch ($userType->value) {
    case UserType::Administrator:
    case new UserType(UserType::Subscriber):
    default:
}
switch ($userType?->value) {
    case UserType::Administrator:
    case null:
    default:
}
switch ($userType->value) {
    case UserType::Administrator:
    case 1:
    default:
}
switch ($mixed) {
    case UserType::Administrator:
    case 1:
    default:
}
switch ('foo') {
    case UserType::Administrator:
    case UserType::Subscriber:
    case $var:
}
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */

switch ($userType) {
    case UserType::Administrator:
    case UserType::Subscriber:
    default:
}
switch ($userType) {
    case UserType::Administrator:
    case null:
    default:
}
switch ($userType->value) {
    case UserType::Administrator->value:
    case 1:
    default:
}
switch ($mixed) {
    case UserType::Administrator->value:
    case 1:
    default:
}
switch ('foo') {
    case UserType::Administrator->value:
    case UserType::Subscriber->value:
    case $var:
}
