<?php

namespace BenSampo\Enum\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS_CONSTANT)]
class Description
{
    public string $description;

    public function __construct(string $description)
    {
        $this->description = $description;
    }
}
