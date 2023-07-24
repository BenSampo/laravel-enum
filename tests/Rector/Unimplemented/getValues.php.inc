<?php

use BenSampo\Enum\Tests\Enums\UserType;

UserType::getValues('Administrator', UserType::Moderator()->key);
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

?
