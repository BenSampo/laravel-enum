<?php

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

    /** some text */
    public function dynamic(): int
    {
        return $this->value;
    }

    /** @return array{} */
    public static function static(): array
    {
        return [];
    }
}
-----
<?php

use BenSampo\Enum\Enum;

/**
 * Uses magic calls to instantiate itself.
 */
enum MagicCall : int
{
    /** Meet you at the... */
    case BAR = 1;
    case BAZ = 2;
    /** some text */
    public function dynamic(): int
    {
        return $this->value;
    }
    /** @return array{} */
    public static function static(): array
    {
        return [];
    }
}
