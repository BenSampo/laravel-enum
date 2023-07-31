<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType|null $maybeUserType */

class Foo {
    public UserType $userType;
}

$foo = $maybeUserType ?? UserType::Administrator();
$qux = UserType::Administrator;
$bar ??= UserType::Administrator;
$baz .= UserType::Administrator;
$ref &= UserType::Administrator;
(new Foo)->userType = UserType::Administrator;
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

/** @var UserType|null $maybeUserType */

class Foo {
    public UserType $userType;
}

$foo = $maybeUserType ?? UserType::Administrator;
$qux = UserType::Administrator->value;
$bar ??= UserType::Administrator->value;
$baz .= UserType::Administrator->value;
$ref &= UserType::Administrator->value;
(new Foo)->userType = UserType::Administrator;
