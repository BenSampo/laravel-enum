<?php

namespace BenSampo\Enum\PHPStan;

use PHPStan\Reflection\ClassMemberReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\FunctionVariant;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ObjectType;

class EnumMethodReflection implements MethodReflection
{
    /**
     * @var \PHPStan\Reflection\ClassReflection
     */
    private $classReflection;

    /**
     * @var string
     */
    private $name;

    public function __construct(ClassReflection $classReflection, string $name)
    {
        $this->classReflection = $classReflection;
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDeclaringClass(): ClassReflection
    {
        return $this->classReflection;
    }

    public function isStatic(): bool
    {
        return true;
    }

    public function isPrivate(): bool
    {
        return false;
    }

    public function isPublic(): bool
    {
        return true;
    }

    public function getPrototype(): ClassMemberReflection
    {
        return $this;
    }

    public function getVariants(): array
    {
        return [
            new FunctionVariant(
                [],
                false,
                new ObjectType($this->classReflection->getName())
            ),
        ];
    }
}
