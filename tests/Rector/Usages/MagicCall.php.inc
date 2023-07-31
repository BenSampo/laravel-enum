<?php

namespace BenSampo\Enum\Tests\Rector\Usages;

use BenSampo\Enum\Enum;

/**
 * Uses magic calls to instantiate itself.
 *
 * @method static static BAR()
 * @method static static BAZ()
 *
 * @extends Enum<int>
 */
class MagicCall extends Enum
{
    /** Meet you at the... */
    const BAR = 1;
    const BAZ = 2;

    /** Magic static. */
    public function static(): static
    {
        return static::BAR();
    }

    /** @return array{self} */
    public static function self(): array
    {
        return [self::BAR()];
    }
}
-----
<?php

namespace BenSampo\Enum\Tests\Rector\Usages;

use BenSampo\Enum\Enum;

/**
 * Uses magic calls to instantiate itself.
 *
 * @method static static BAR()
 * @method static static BAZ()
 *
 * @extends Enum<int>
 */
class MagicCall extends Enum
{
    /** Meet you at the... */
    const BAR = 1;
    const BAZ = 2;

    /** Magic static. */
    public function static(): static
    {
        return static::BAR;
    }

    /** @return array{self} */
    public static function self(): array
    {
        return [self::BAR];
    }
}
