<?php

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\PHPStan\EnumMethodsClassReflectionExtension;
use BenSampo\Enum\Tests\Enums\AnnotatedConstants;
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

    protected function setUp(): void
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

    public function test_enum_method_reflection_hasSideEffects_returns_no(): void
    {
        $method = $this->getMethodReflection(UserType::class, 'Administrator');
        $this->assertTrue($method->hasSideEffects()->no(), 'hasSideEffects should return TrinaryLogic::No');
    }

    public function test_enum_method_reflection_isFinal_returns_no(): void
    {
        $method = $this->getMethodReflection(UserType::class, 'Administrator');
        $this->assertTrue($method->isFinal()->no(), 'isFinal should return TrinaryLogic::No');
    }

    public function test_internal_deprecated_constant_static_method_is_internal_and_deprecated(): void
    {
        $method = $this->getMethodReflection(AnnotatedConstants::class, 'InternalDeprecated');

        $this->assertTrue($method->isInternal()->yes(), 'isInternal should return TrinaryLogic::Yes');
        $this->assertTrue($method->isDeprecated()->yes(), 'isDeprecated should return TrinaryLogic::Yes');
    }

    public function test_internal_deprecated_constant_static_method_deprecation_message(): void
    {
        $method = $this->getMethodReflection(AnnotatedConstants::class, 'InternalDeprecated');

        $deprecatedDescription = $method->getDeprecatedDescription();
        $this->assertEquals('1.0 Deprecation description', $deprecatedDescription);
    }

    public function test_internal_constant_static_method_is_internal(): void
    {
        $method = $this->getMethodReflection(AnnotatedConstants::class, 'Internal');

        $this->assertTrue($method->isInternal()->yes(), 'isInternal should return TrinaryLogic::Yes');
        $this->assertFalse($method->isDeprecated()->yes(), 'isDeprecated should return TrinaryLogic::No');
    }

    public function test_deprecated_constant_static_method_is_deprecated(): void
    {
        $method = $this->getMethodReflection(AnnotatedConstants::class, 'Deprecated');

        $this->assertFalse($method->isInternal()->yes(), 'isInternal should return TrinaryLogic::No');
        $this->assertTrue($method->isDeprecated()->yes(), 'isDeprecated should return TrinaryLogic::Yes');
    }

    public function test_deprecated_constant_static_method_deprecation_message(): void
    {
        $method = $this->getMethodReflection(AnnotatedConstants::class, 'Deprecated');

        $deprecatedDescription = $method->getDeprecatedDescription();
        $this->assertEquals('', $deprecatedDescription);
    }

    public function test_unannotated_constant_static_method_is_not_internal_and_not_deprecated(): void
    {
        $method = $this->getMethodReflection(AnnotatedConstants::class, 'Unannotated');

        $this->assertFalse($method->isInternal()->yes(), 'isInteral should return TrinaryLogic::No');
        $this->assertFalse($method->isDeprecated()->yes(), 'isDeprecated should return TrinaryLogic::No');
    }

    public function test_unnanotated_constant_static_method_deprecated_message_is_null(): void
    {
        $method = $this->getMethodReflection(AnnotatedConstants::class, 'Unannotated');

        $this->assertNull($method->getDeprecatedDescription());
    }

    public function test_getVariants_returns_array(): void
    {
        $method = $this->getMethodReflection(UserType::class, 'Administrator');
        $this->assertIsArray($method->getVariants());
    }

    protected function getMethodReflection(string $class, string $name): MethodReflection
    {
        $broker = $this->createBroker();
        return $this->reflectionExtension->getMethod($broker->getClass($class), $name);
    }
}
