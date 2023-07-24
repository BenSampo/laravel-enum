<?php

namespace BenSampo\Enum\Tests\Rector\Fixtures;

use BenSampo\Enum\Enum;

class MagicCall extends Enum {
    const BAR = 1;
    const BAZ = 2;

    public function static(): self
    {
        return static::BAR();
    }

    public function self(): self
    {
        return self::BAR();
    }
}
-----
<?php

namespace BenSampo\Enum\Tests\Rector\Fixtures;

use BenSampo\Enum\Enum;

class MagicCall extends Enum {
    const BAR = 1;
    const BAZ = 2;

    public function static(): self
    {
        return static::BAR;
    }

    public function self(): self
    {
        return self::BAR;
    }
}
