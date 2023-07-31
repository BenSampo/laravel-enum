<?php

use BenSampo\Enum\Tests\Enums\UserType;

UserType::getKeys(UserType::Administrator, UserType::Moderator);
UserType::getKeys([UserType::Administrator, UserType::Moderator]);
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

[\BenSampo\Enum\Tests\Enums\UserType::Administrator->name, \BenSampo\Enum\Tests\Enums\UserType::Moderator->name];
[\BenSampo\Enum\Tests\Enums\UserType::Administrator->name, \BenSampo\Enum\Tests\Enums\UserType::Moderator->name];
