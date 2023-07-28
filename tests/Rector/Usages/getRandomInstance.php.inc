<?php

use BenSampo\Enum\Tests\Enums\UserType;

UserType::getRandomInstance();
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

fake()->randomElement(UserType::cases());
