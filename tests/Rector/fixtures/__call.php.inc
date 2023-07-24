<?php

use BenSampo\Enum\Enum;

class Foo extends Enum {
    const BAR = 1;
    const BAZ = 2;

    public function convert(): self
    {
        return static::BAR();
    }
}
-----
<?php

use BenSampo\Enum\Enum;

class Foo extends Enum {
    const BAR = 1;
    const BAZ = 2;

    public function convert(): self
    {
        return static::BAR();
    }
}
