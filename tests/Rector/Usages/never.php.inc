<?php

namespace BenSampo\Enum\Tests\Rector\Usages;

// Put something here that changes to avoid warnings
use BenSampo\Enum\Tests\Enums\UserType;
new UserType(UserType::Administrator);

// False-positive never type
get_class(0) . '';
-----
<?php

namespace BenSampo\Enum\Tests\Rector\Usages;

// Put something here that changes to avoid warnings
use BenSampo\Enum\Tests\Enums\UserType;
UserType::Administrator;

// False-positive never type
get_class(0) . '';
