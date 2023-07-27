<?php

use BenSampo\Enum\Tests\Enums\UserType;

class Foo {
    public UserType $userType;
}

$foo = UserType::Administrator;
$bar ??= UserType::Administrator;
$baz .= UserType::Administrator;
$ref &= UserType::Administrator;
(new Foo)->userType = UserType::Administrator;

-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

class Foo {
    public UserType $userType;
}

$foo = UserType::Administrator->value;
$bar ??= UserType::Administrator->value;
$baz .= UserType::Administrator->value;
$ref &= UserType::Administrator->value;
(new Foo)->userType = UserType::Administrator;
