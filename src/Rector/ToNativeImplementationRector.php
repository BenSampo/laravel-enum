<?php declare(strict_types=1);

namespace BenSampo\Enum\Rector;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Tests\Enums\UserType;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\EnumCase;
use PHPStan\PhpDocParser\Ast\PhpDoc\ExtendsTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueNode;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use Rector\BetterPhpDocParser\Printer\PhpDocInfoPrinter;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \BenSampo\Enum\Tests\Rector\ToNativeRectorImplementationTest
 */
class ToNativeImplementationRector extends ToNativeRector
{
    public function __construct(
        protected PhpDocInfoPrinter $phpDocInfoPrinter,
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Convert usages of BenSampo\Enum\Enum to native PHP enums', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
/**
 * @method static static ADMIN()
 * @method static static MEMBER()
 *
 * @extends Enum<int>
 */
class UserType extends Enum
{
    const ADMIN = 1;
    const MEMBER = 2;
}
CODE_SAMPLE

                ,
                <<<'CODE_SAMPLE'
enum UserType : int
{
    case ADMIN = 1;
    case MEMBER = 2;
}
CODE_SAMPLE,
                [
                    UserType::class,
                ],
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @see \Rector\Php81\NodeFactory\EnumFactory
     *
     * @param Class_ $class
     */
    public function refactor(Node $class): ?Node
    {
        $this->classes ??= [new ObjectType(Enum::class)];

        if (! $this->inConfiguredClasses($class)) {
            return null;
        }

        $enum = new Enum_(
            $this->nodeNameResolver->getShortName($class),
            [],
            ['startLine' => $class->getStartLine(), 'endLine' => $class->getEndLine()]
        );
        $enum->namespacedName = $class->namespacedName;

        $phpDocInfo = $this->phpDocInfoFactory->createFromNode($class);
        if ($phpDocInfo) {
            $phpDocInfo->removeByType(MethodTagValueNode::class);
            $phpDocInfo->removeByType(ExtendsTagValueNode::class);

            $phpdoc = $this->phpDocInfoPrinter->printFormatPreserving($phpDocInfo);
            // By removing unnecessary tags, we are usually left with a couple of redundant newlines.
            // There might be valuable ones to keep in long descriptions which will unfortunately
            // also be removed, but this should be less common.
            $withoutEmptyNewlines = preg_replace('/ \*\n/', '', $phpdoc);
            if ($withoutEmptyNewlines) {
                $enum->setDocComment(new Doc($withoutEmptyNewlines));
            }
        }

        $enum->stmts = $class->getTraitUses();

        $constants = $class->getConstants();
        if ($constants !== []) {
            // Assume the first constant value has the correct type
            $value = $this->valueResolver->getValue($constants[0]->consts[0]->value);
            $enum->scalarType = is_string($value)
                ? new Identifier('string')
                : new Identifier('int');

            foreach ($constants as $constant) {
                $constConst = $constant->consts[0];
                $enumCase = new EnumCase(
                    $constConst->name,
                    $constConst->value,
                    [],
                    ['startLine' => $constConst->getStartLine(), 'endLine' => $constConst->getEndLine()],
                );

                // mirror comments
                $enumCase->setAttribute(AttributeKey::PHP_DOC_INFO, $constant->getAttribute(AttributeKey::PHP_DOC_INFO));
                $enumCase->setAttribute(AttributeKey::COMMENTS, $constant->getAttribute(AttributeKey::COMMENTS));

                $enum->stmts[] = $enumCase;
            }
        }

        $enum->stmts = [...$enum->stmts, ...$class->getMethods()];

        return $enum;
    }
}
