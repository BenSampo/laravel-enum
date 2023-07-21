<?php declare(strict_types=1);

namespace BenSampo\Enum\Rector;

use BenSampo\Enum\Enum;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PHPStan\Type\ObjectType;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class ToNativeRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Convert usages of BenSampo\Enum\Enum to native PHP enums', [
            new CodeSample(
                <<<'CODE_SAMPLE'
$user = User::ADMIN();
$user->is(User::ADMIN);
CODE_SAMPLE

                ,
                <<<'CODE_SAMPLE'
$user = User::ADMIN;
$user === User::ADMIN;
CODE_SAMPLE
            ),
        ]);
    }

    /** @return array<class-string<Expr>> */
    public function getNodeTypes(): array
    {
        return [MethodCall::class, NullsafeMethodCall::class];
    }

    /** @param MethodCall|NullsafeMethodCall $node */
    public function refactor(Node $node): ?Node
    {
        if (! $this->isObjectType($node->var, new ObjectType(Enum::class))) {
            return null;
        }

        if ($this->isName($node->name, 'is')) {
            return $this->refactorIs($node);
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
}
