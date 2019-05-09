<?php

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\PHPStan\EnumMethodsClassReflectionExtension;
use BenSampo\Enum\Tests\Enums\UserType;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Testing\TestCase;

class PHPStanTest extends TestCase
{
    /**
     * @var \BenSampo\Enum\PHPStan\EnumMethodsClassReflectionExtension
     */
    private $reflectionExtension;

    /**
     * @var \PHPStan\Reflection\ClassReflection
     */
    private $enumReflection;

    protected function setUp()
    {
        parent::setUp();

        $broker = $this->createBroker();
        $this->enumReflection = $broker->getClass(UserType::class);

        $this->reflectionExtension = new EnumMethodsClassReflectionExtension();
    }

    public function test_recognizes_magic_static_methods()
    {
        $this->assertTrue(
            $this->reflectionExtension->hasMethod($this->enumReflection, 'Administrator')
        );

        $this->assertFalse(
            $this->reflectionExtension->hasMethod($this->enumReflection, 'FooBar')
        );
    }

    public function test_get_enum_method_reflection()
    {
        $this->assertInstanceOf(
            MethodReflection::class,
            $this->reflectionExtension->getMethod($this->enumReflection, 'Administrator')
        );
    }
}
