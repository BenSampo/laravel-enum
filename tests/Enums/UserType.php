<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Administrator()
 * @method static static Moderator()
 * @method static static Subscriber()
 * @method static static SuperAdministrator()
 *
 * @extends Enum<self::*>
 */
final class UserType extends Enum
{
    public const Administrator = 0;
    public const Moderator = 1;
    public const Subscriber = 2;
    public const SuperAdministrator = 3;

    public function magicInstantiationFromInstanceMethod(): self
    {
        return self::Administrator();
    }
}
