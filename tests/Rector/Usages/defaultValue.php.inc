<?php

use BenSampo\Enum\Tests\Enums\UserType;

function foo(string $a = UserType::Administrator) {}
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

function foo(string $a = UserType::Administrator->value) {}
