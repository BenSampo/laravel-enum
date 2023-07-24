<?php declare(strict_types=1);

namespace BenSampo\Enum\Rector;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Tests\Enums\UserType;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PHPStan\Type\ObjectType;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Rector\StaticTypeMapper\ValueObject\Type\FullyQualifiedObjectType;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/** @see \BenSampo\Enum\Tests\Rector\ToNativeRectorTest */
class ToNativeRector extends AbstractRector implements ConfigurableRuleInterface, ConfigurableRectorInterface
{
    /** @var array<ObjectType> */
    protected array $classes;

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Convert usages of BenSampo\Enum\Enum to native PHP enums', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
$user = UserType::ADMIN();
$user->is(UserType::ADMIN);
CODE_SAMPLE

                ,
                <<<'CODE_SAMPLE'
$user = UserType::ADMIN;
$user === UserType::ADMIN;
CODE_SAMPLE,
                [
                    'classes' => [
                        UserType::class,
                    ],
                ],
            ),
        ]);
    }

    /** @param array{classes: array<class-string>} $configuration */
    public function configure(array $configuration): void
    {
        $this->classes = array_map(
            static fn (string $class): ObjectType => new ObjectType($class),
            $configuration['classes'],
        );
    }

    /** @return array<class-string<Expr>> */
    public function getNodeTypes(): array
    {
        return [
            MethodCall::class,
            NullsafeMethodCall::class,
            New_::class,
            StaticCall::class,
        ];
    }

    public function refactor(Node $node): ?Node
    {
        $this->classes ??= [new ObjectType(Enum::class)];

        foreach ($this->classes as $class) {
            if ($this->isConfiguredClass($node, $class)) {
                return $this->refactorNode($node);
            }
        }

        return null;
    }

    protected function isConfiguredClass(Node $node, ObjectType $class): bool
    {
        if ($node instanceof MethodCall || $node instanceof NullsafeMethodCall) {
            return $this->isObjectType($node->var, $class);
        }

        if ($node instanceof New_ || $node instanceof StaticCall) {
            return $this->isObjectType($node->class, $class);
        }

        return false;
    }

    protected function refactorNode(Node $node): ?Node
    {
        if ($node instanceof MethodCall || $node instanceof NullsafeMethodCall) {
            if ($this->isName($node->name, 'is')) {
                return $this->refactorIs($node);
            }

            if ($this->isName($node->name, 'isNot')) {
                return $this->refactorIsNot($node);
            }

            if ($this->isName($node->name, 'in')) {
                return $this->refactorIn($node);
            }

            if ($this->isName($node->name, 'notIn')) {
                return $this->refactorNotIn($node);
            }
        }

        if ($node instanceof StaticCall) {
            if ($this->isName($node->name, 'fromValue')) {
                return $this->refactorNewOrFromValue($node);
            }

            if ($this->isName($node->name, 'getInstances')) {
                return $this->refactorGetInstances($node);
            }

            if ($this->isName($node->name, 'getKeys')) {
                return $this->refactorGetKeys($node);
            }

            if ($this->isName($node->name, 'getValues')) {
                return $this->refactorGetValues($node);
            }

            return $this->refactorMagicStaticCall($node);
        }

        if ($node instanceof New_) {
            return $this->refactorNewOrFromValue($node);
        }

        return null;
    }

    /** @see Enum::is() */
    protected function refactorIs(MethodCall|NullsafeMethodCall $node): ?Node
    {
        $args = $node->args;
        if (isset($args[0]) && $args[0] instanceof Arg) {
            $arg = $args[0];

            return new Identical($node->var, $arg->value);
        }

        return null;
    }

    /** @see Enum::isNot() */
    protected function refactorIsNot(MethodCall|NullsafeMethodCall $node): ?Node
    {
        $args = $node->args;
        if (isset($args[0]) && $args[0] instanceof Arg) {
            $arg = $args[0];

            return new NotIdentical($node->var, $arg->value);
        }

        return null;
    }

    /** @see Enum::in() */
    protected function refactorIn(MethodCall|NullsafeMethodCall $node): ?Node
    {
        $args = $node->args;
        if (isset($args[0]) && $args[0] instanceof Arg) {
            $arg = $args[0];

            return $this->nodeFactory->createFuncCall('in_array', [$node->var, $arg]);
        }

        return null;
    }

    /** @see Enum::notIn() */
    protected function refactorNotIn(MethodCall|NullsafeMethodCall $node): ?Node
    {
        $args = $node->args;
        if (isset($args[0]) && $args[0] instanceof Arg) {
            $arg = $args[0];

            return new BooleanNot(
                $this->nodeFactory->createFuncCall('in_array', [$node->var, $arg])
            );
        }

        return null;
    }

    /**
     * @see Enum::__construct()
     * @see Enum::fromValue()
     */
    protected function refactorNewOrFromValue(New_|StaticCall $node): ?Node
    {
        $class = $node->class;
        if ($class instanceof Name) {
            $args = $node->args;
            if (isset($args[0]) && $args[0] instanceof Arg) {
                $classString = $class->toString();

                $argValue = $args[0]->value;
                if ($argValue instanceof ClassConstFetch) {
                    $argValueClass = $argValue->class;
                    $argValueName = $argValue->name;
                    if (
                        $argValueClass instanceof Name
                        && $argValueClass->toString() === $classString
                        && $argValueName instanceof Identifier
                    ) {
                        return $this->nodeFactory->createClassConstFetch($classString, $argValueName->name);
                    }
                }

                return $this->nodeFactory->createStaticCall($classString, 'from', [$argValue]);
            }
        }

        return null;
    }

    /** @see Enum::getInstances() */
    protected function refactorGetInstances(StaticCall $node): ?Node
    {
        $class = $node->class;
        if ($class instanceof Name) {
            return $this->nodeFactory->createStaticCall($class->toString(), 'cases');
        }

        return null;
    }

    /** @see Enum::getKeys() */
    protected function refactorGetKeys(StaticCall $node): ?Node
    {
        $class = $node->class;
        if ($class instanceof Name) {
            $args = $node->args;
            if ($args === []) {
                $paramName = lcfirst($class->getLast());
                $paramVariable = new Variable($paramName);

                return $this->nodeFactory->createFuncCall('array_map', [
                    new ArrowFunction([
                        'static' => true,
                        'params' => [new Param($paramVariable, null, $class)],
                        'returnType' => 'string',
                        'expr' => new PropertyFetch($paramVariable, 'name'),
                    ]),
                    $this->nodeFactory->createStaticCall($class->toString(), 'cases'),
                ]);
            }
        }

        return null;
    }

    /** @see Enum::getValues() */
    protected function refactorGetValues(StaticCall $node): ?Node
    {
        $class = $node->class;
        if ($class instanceof Name) {
            $args = $node->args;
            if ($args === []) {
                $paramName = lcfirst($class->getLast());
                $paramVariable = new Variable($paramName);

                return $this->nodeFactory->createFuncCall('array_map', [
                    new ArrowFunction([
                        'static' => true,
                        'params' => [new Param($paramVariable, null, $class)],
                        'expr' => new PropertyFetch($paramVariable, 'value'),
                    ]),
                    $this->nodeFactory->createStaticCall($class->toString(), 'cases'),
                ]);
            }
        }

        return null;
    }

    /**
     * @see Enum::__callStatic()
     * @see Enum::__call()
     */
    protected function refactorMagicStaticCall(StaticCall $node): ?Node
    {
        $name = $node->name;
        if ($name instanceof Expr) {
            return null;
        }

        $class = $node->class;
        if ($class instanceof Name) {
            if ($class->isSpecialClassName()) {
                $type = $this->getType($class);
                if (! $type instanceof FullyQualifiedObjectType) {
                    return null;
                }
                $fullyQualifiedClassName = $type->getClassName();
            } else {
                $fullyQualifiedClassName = $class->toString();
            }
            $constName = $name->toString();
            if (defined("{$fullyQualifiedClassName}::{$constName}")) {
                return $this->nodeFactory->createClassConstFetch($class->toString(), $constName);
            }
        }

        return null;
    }
}
