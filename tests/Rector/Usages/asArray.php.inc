<?php

use BenSampo\Enum\Tests\Enums\UserType;

UserType::asArray();
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

array_reduce(UserType::cases(), static function (array $result, UserType $userType) {
    $result[$userType->name] = $userType->value;
    return $result;
}, []);
