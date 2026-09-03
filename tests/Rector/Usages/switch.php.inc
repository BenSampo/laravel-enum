<?php

use BenSampo\Enum\Tests\Enums\StringValues;
use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */
/** @var StringValues|UserType $union */
/** @var UserType|null $nullableUserType */
/** @var StringValues|UserType $sameUnion */

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
switch ($union->value) {
    case $userType:
    default:
}
switch ($nullableUserType->value) {
    case 'foo':
    default:
}
switch ($union->value) {
    case $sameUnion:
    default:
}
-----
<?php

use BenSampo\Enum\Tests\Enums\StringValues;
use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType $userType */
/** @var StringValues|UserType $union */
/** @var UserType|null $nullableUserType */
/** @var StringValues|UserType $sameUnion */

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
switch ($union->value) {
    case $userType->value:
    default:
}
switch ($nullableUserType->value) {
    case 'foo':
    default:
}
switch ($union->value) {
    case $sameUnion->value:
    default:
}
