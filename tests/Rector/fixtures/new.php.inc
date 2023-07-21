<?php

use BenSampo\Enum\Tests\Enums\UserType;

new UserType('foo');
-----
<?php

use BenSampo\Enum\Tests\Enums\UserType;

\BenSampo\Enum\Tests\Enums\UserType::from('foo');
