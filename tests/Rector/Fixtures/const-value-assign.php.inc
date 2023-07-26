<?php

use BenSampo\Enum\Tests\Enums\UserType;

$foo = UserType::Administrator;
$bar ??= UserType::Administrator;
$baz .= UserType::Administrator;
$ref &= UserType::Administrator;

-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

$foo = UserType::Administrator->value;
$bar ??= UserType::Administrator->value;
$baz .= UserType::Administrator->value;
$ref &= UserType::Administrator->value;
