<?php declare(strict_types=1);

namespace BenSampo\Enum\Rector;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Tests\Enums\UserType;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PHPStan\Type\ObjectType;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/** @see \BenSampo\Enum\Tests\Rector\ToNativeRectorTest */
class ToNativeRector extends AbstractRector implements ConfigurableRuleInterface
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
        return [MethodCall::class, NullsafeMethodCall::class];
    }

    /** @param MethodCall|NullsafeMethodCall $node */
    public function refactor(Node $node): ?Node
    {
        $this->classes ??= [new ObjectType(Enum::class)];

        foreach ($this->classes as $class) {
            if ($this->isObjectType($node->var, $class)) {
                return $this->doRefactor($node);
            }
        }

        return null;
    }

    protected function doRefactor(MethodCall|NullsafeMethodCall $node): ?Node
    {
        if ($this->isName($node->name, 'is')) {
            return $this->refactorIs($node);
        }

        if ($this->isName($node->name, 'in')) {
            return $this->refactorIn($node);
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

            return $this->nodeFactory->createFuncCall('in_array', [$node->var, $arg->value]);
        }

        return null;
    }
}
