<?php

namespace BenSampo\Enum\Tests\Rector\Fixtures;

use BenSampo\Enum\Enum;

/**
 * Uses magic calls to instantiate itself.
 *
 * @method static static BAR()
 * @method static static BAZ()
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

namespace BenSampo\Enum\Tests\Rector\Fixtures;

use BenSampo\Enum\Enum;

/**
 * Uses magic calls to instantiate itself.
 */
enum MagicCall : int
{
    /** Meet you at the... */
    case BAR = 1;
    case BAZ = 2;
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
