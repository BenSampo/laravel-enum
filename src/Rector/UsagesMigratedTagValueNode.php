<?php

namespace BenSampo\Enum\Rector;

use PHPStan\PhpDocParser\Ast\NodeAttributes;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode;

final class UsagesMigratedTagValueNode implements PhpDocTagValueNode
{
    use NodeAttributes;

    public function __toString(): string
    {
        return ToNativeRector::USAGES_MIGRATED;
    }
}
