<?php

use BenSampo\Enum\Tests\Enums\UserType;

UserType::Administrator > 3;
2 * UserType::Subscriber;
2 | UserType::Subscriber->value;

-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

UserType::Administrator->value > 3;
2 * UserType::Subscriber->value;
2 | UserType::Subscriber->value;
