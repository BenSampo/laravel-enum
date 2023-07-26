<?php

use BenSampo\Enum\Tests\Enums\UserType;

UserType::getInstances();
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

\BenSampo\Enum\Tests\Enums\UserType::cases();
