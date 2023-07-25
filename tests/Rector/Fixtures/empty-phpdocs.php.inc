<?php

use BenSampo\Enum\Enum;

/**
 * @method static static BAR()
 * @method static static BAZ()
 *
 * @extends Enum<int>
 * @usages-migrated
 */
class Foo extends Enum
{
    const BAR = 1;
}
-----
<?php

use BenSampo\Enum\Enum;

enum Foo : int
{
    case BAR = 1;
}
