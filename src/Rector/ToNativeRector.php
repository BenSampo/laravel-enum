<?php declare(strict_types=1);

namespace BenSampo\Enum\Rector;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Tests\Enums\UserType;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Type\ObjectType;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
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

            if ($this->isName($node->name, 'in')) {
                return $this->refactorIn($node);
            }
        }

        if ($node instanceof StaticCall) {
            if ($this->isName($node->name, 'fromValue')) {
                return $this->refactorNewOrFromValue($node);
            }
        }

        if ($node instanceof New_) {
            return $this->refactorNewOrFromValue($node);
        }

        return null;
    }

    protected function refactorIs(MethodCall|NullsafeMethodCall $node): ?Node
    {
        $args = $node->args;
        if (isset($args[0]) && $args[0] instanceof Arg) {
            $arg = $args[0];

            return new Identical($node->var, $arg->value);
        }

        return null;
    }

    protected function refactorIn(MethodCall|NullsafeMethodCall $node): ?Node
    {
        $args = $node->args;
        if (isset($args[0]) && $args[0] instanceof Arg) {
            $arg = $args[0];

            return $this->nodeFactory->createFuncCall('in_array', [$node->var, $arg]);
        }

        return null;
    }

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
}