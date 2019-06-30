<?php
declare(strict_types=1);

namespace BenSampo\Enum\Docblock;

use phpDocumentor\Reflection\Type;

class EnumPropertyType implements Type
{
    /** @var string */
    private $enumClassName;

    public function __construct(string $enumClassName)
    {
        $this->enumClassName = $enumClassName;
    }

    public function __toString()
    {
        return sprintf('\%s|null', $this->enumClassName);
    }
}