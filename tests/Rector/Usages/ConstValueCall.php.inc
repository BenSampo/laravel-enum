<?php

namespace BenSampo\Enum\Tests\Rector\Usages;

use BenSampo\Enum\Tests\Enums\UserType;

class ConstValueCall {
    public function __construct(int $value) {}
    public static function int(int $value): void {}
    public function maybeInt(int|null $value): void {}
}
$intOrString = fn (int|string $value) => null;

ceil(...);
ceil(UserType::Administrator);
(new ConstValueCall(UserType::Administrator))->maybeInt(UserType::Administrator);
ConstValueCall::int(UserType::Administrator);
$intOrString(UserType::Administrator);
$intOrString(UserType::class);
-----
<?php

namespace BenSampo\Enum\Tests\Rector\Usages;

use BenSampo\Enum\Tests\Enums\UserType;

class ConstValueCall {
    public function __construct(int $value) {}
    public static function int(int $value): void {}
    public function maybeInt(int|null $value): void {}
}
$intOrString = fn (int|string $value) => null;

ceil(...);
ceil(UserType::Administrator->value);
(new ConstValueCall(UserType::Administrator->value))->maybeInt(UserType::Administrator->value);
ConstValueCall::int(UserType::Administrator->value);
$intOrString(UserType::Administrator->value);
$intOrString(UserType::class);
