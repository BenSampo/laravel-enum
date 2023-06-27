<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\PHPStan\EnumMethodsClassReflectionExtension;
use BenSampo\Enum\Tests\Enums\AnnotatedConstants;
use BenSampo\Enum\Tests\Enums\UserType;
use PHPStan\Reflection\ClassReflection as PHPStanClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Testing\PHPStanTestCase;

final class PHPStanTest extends PHPStanTestCase
{
    private EnumMethodsClassReflectionExtension $reflectionExtension;

    private PHPStanClassReflection $enumReflection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->enumReflection = $this->createReflectionProvider()->getClass(UserType::class);
        $this->reflectionExtension = new EnumMethodsClassReflectionExtension();
    }

    public function testRecognizesMagicStaticMethods(): void
    {
        $this->assertTrue(
            $this->reflectionExtension->hasMethod($this->enumReflection, 'Administrator')
        );

        $this->assertFalse(
            $this->reflectionExtension->hasMethod($this->enumReflection, 'FooBar')
        );
    }

    public function testEnumMethodReflectionHasSideEffectsReturnsNo(): void
    {
        $method = $this->getMethodReflection(UserType::class, 'Administrator');

        $this->assertTrue($method->hasSideEffects()->no(), 'hasSideEffects should return TrinaryLogic::No');
    }

    public function testEnumMethodReflectionIsFinalReturnsNo(): void
    {
        $method = $this->getMethodReflection(UserType::class, 'Administrator');

        $this->assertTrue($method->isFinal()->no(), 'isFinal should return TrinaryLogic::No');
    }

    public function testInternalDeprecatedConstantStaticMethodIsInternalAndDeprecated(): void
    {
        $method = $this->getMethodReflection(AnnotatedConstants::class, 'InternalDeprecated');

        $this->assertTrue($method->isInternal()->yes(), 'isInternal should return TrinaryLogic::Yes');
        $this->assertTrue($method->isDeprecated()->yes(), 'isDeprecated should return TrinaryLogic::Yes');
    }

    public function testInternalDeprecatedConstantStaticMethodDeprecationMessage(): void
    {
        $method = $this->getMethodReflection(AnnotatedConstants::class, 'InternalDeprecated');

        $this->assertSame('1.0 Deprecation description', $method->getDeprecatedDescription());
    }

    public function testInternalConstantStaticMethodIsInternal(): void
    {
        $method = $this->getMethodReflection(AnnotatedConstants::class, 'Internal');

        $this->assertTrue($method->isInternal()->yes(), 'isInternal should return TrinaryLogic::Yes');
        $this->assertFalse($method->isDeprecated()->yes(), 'isDeprecated should return TrinaryLogic::No');
    }

    public function testDeprecatedConstantStaticMethodIsDeprecated(): void
    {
        $method = $this->getMethodReflection(AnnotatedConstants::class, 'Deprecated');

        $this->assertFalse($method->isInternal()->yes(), 'isInternal should return TrinaryLogic::No');
        $this->assertTrue($method->isDeprecated()->yes(), 'isDeprecated should return TrinaryLogic::Yes');
    }

    public function testDeprecatedConstantStaticMethodDeprecationMessage(): void
    {
        $method = $this->getMethodReflection(AnnotatedConstants::class, 'Deprecated');

        $this->assertNull($method->getDeprecatedDescription());
    }

    public function testUnannotatedConstantStaticMethodIsNotInternalAndNotDeprecated(): void
    {
        $method = $this->getMethodReflection(AnnotatedConstants::class, 'Unannotated');

        $this->assertFalse($method->isInternal()->yes(), 'isInternal should return TrinaryLogic::No');
        $this->assertFalse($method->isDeprecated()->yes(), 'isDeprecated should return TrinaryLogic::No');
    }

    public function testUnannotatedConstantStaticMethodDeprecatedMessageIsNull(): void
    {
        $method = $this->getMethodReflection(AnnotatedConstants::class, 'Unannotated');

        $this->assertNull($method->getDeprecatedDescription());
    }

    protected function getMethodReflection(string $class, string $name): MethodReflection
    {
        return $this->reflectionExtension->getMethod(
            $this->createReflectionProvider()->getClass($class),
            $name
        );
    }
}
