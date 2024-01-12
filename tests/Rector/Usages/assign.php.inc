<?php

use BenSampo\Enum\Tests\Enums\UserType;

class Foo {
    public UserType $userType;
}

/** @var UserType|null $maybeUserType */
$foo = $maybeUserType ?? UserType::Administrator();
(new Foo)->userType = UserType::Administrator();
$foo = new Foo;
$foo->userType = UserType::Administrator();
/** @var UserType $userType */
$userType = UserType::Administrator();

$qux = UserType::Administrator;
$bar ??= UserType::Administrator;
$baz .= UserType::Administrator;
$ref &= UserType::Administrator;
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

class Foo {
    public UserType $userType;
}

/** @var UserType|null $maybeUserType */
$foo = $maybeUserType ?? UserType::Administrator;
(new Foo)->userType = UserType::Administrator;
$foo = new Foo;
$foo->userType = UserType::Administrator;
/** @var UserType $userType */
$userType = UserType::Administrator;

$qux = UserType::Administrator->value;
$bar ??= UserType::Administrator->value;
$baz .= UserType::Administrator->value;
$ref &= UserType::Administrator->value;
