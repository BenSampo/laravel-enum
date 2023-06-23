<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Enums\ToNative;

enum UserType: int
{
    case Administrator = 0;
    case Moderator = 1;
}
