<?php declare(strict_types=1);

namespace BenSampo\Enum\Rector;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Tests\Enums\UserType;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\AssignOp;
use PhpParser\Node\Expr\AssignRef;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\BinaryOp\Equal;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\NotEqual;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\Match_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\MatchArm;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\EnumCase;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDocParser\Ast\PhpDoc\ExtendsTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueNode;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTagRemover;
use Rector\BetterPhpDocParser\Printer\PhpDocInfoPrinter;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractScopeAwareRector;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\StaticTypeMapper\ValueObject\Type\FullyQualifiedObjectType;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \BenSampo\Enum\Tests\Rector\ToNativeRectorUsagesTest
 * @see \BenSampo\Enum\Tests\Rector\ToNativeRectorImplementationTest
 */
class ToNativeRector extends AbstractScopeAwareRector implements ConfigurableRuleInterface, ConfigurableRectorInterface
{
    public const IMPLEMENTATION = 'implementation';
    public const USAGES = 'usages';

    /** @var 'implementation'|'usages' */
    protected string $mode;

    /** @var array<ObjectType> */
    protected array $classes;

    public function __construct(
        protected PhpDocInfoPrinter $phpDocInfoPrinter,
        protected PhpDocTagRemover $phpDocTagRemover,
    ) {}

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
                    'mode' => ToNativeRector::USAGES,
                    'classes' => [UserType::class],
                ],
            ),
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
                    'mode' => ToNativeRector::IMPLEMENTATION,
                    'classes' => [UserType::class],
                ],
            ),
        ]);
    }

    /**
     * @param array{
     *   mode: 'implementation'|'usages',
     *   classes?: array<class-string>|null
     * } $configuration
     */
    public function configure(array $configuration): void
    {
        $this->mode = $configuration['mode'];

        $classes = $configuration['classes'] ?? null;
        if ($classes) {
            $this->classes = array_map(
                static fn (string $class): ObjectType => new ObjectType($class),
                $classes,
            );
        }
    }

    public function getNodeTypes(): array
    {
        return match ($this->mode) {
            ToNativeRector::USAGES => [
                New_::class,
                ClassConstFetch::class,
                ArrayItem::class,
                BinaryOp::class,
                Assign::class,
                AssignOp::class,
                AssignRef::class,
                Match_::class,
                StaticCall::class,
                MethodCall::class,
                NullsafeMethodCall::class,
                PropertyFetch::class,
            ],
            ToNativeRector::IMPLEMENTATION => [
                Class_::class,
            ],
        };
    }

    public function refactorWithScope(Node $node, Scope $scope): ?Node
    {
        $this->classes ??= [new ObjectType(Enum::class)];

        if ($node instanceof Class_) {
            return $this->refactorClass($node);
        }

        if ($node instanceof New_) {
            return $this->refactorNewOrFromValue($node);
        }

        if ($node instanceof ArrayItem) {
            return $this->refactorArrayItem($node);
        }

        if ($node instanceof BinaryOp) {
            return $this->refactorBinaryOp($node, $scope);
        }

        if ($node instanceof Assign || $node instanceof AssignOp || $node instanceof AssignRef) {
            return $this->refactorAssign($node);
        }

        if ($node instanceof Match_) {
            return $this->refactorMatch($node, $scope);
        }

        if ($node instanceof ClassConstFetch) {
            return $this->refactorClassConstFetch($node, $scope);
        }

        if ($node instanceof StaticCall) {
            if (! $this->inConfiguredClasses($node->class)) {
                return null;
            }

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

        if ($node instanceof MethodCall || $node instanceof NullsafeMethodCall) {
            if (! $this->inConfiguredClasses($node->var)) {
                return null;
            }

            if ($this->isName($node->name, 'is')) {
                return $this->refactorIs($node, $scope);
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

        if ($node instanceof PropertyFetch) {
            if (! $this->inConfiguredClasses($node->var)) {
                return null;
            }

            if ($this->isName($node->name, 'key')) {
                return $this->refactorKey($node);
            }
        }

        return null;
    }

    protected function inConfiguredClasses(Node $node): bool
    {
        foreach ($this->classes as $class) {
            if ($this->isObjectType($node, $class)) {
                return true;
            }
        }

        return false;
    }

    /** @see \Rector\Php81\NodeFactory\EnumFactory */
    protected function refactorClass(Class_ $class): ?Node
    {
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

        $constants = $class->getConstants();

        $enum->stmts = $class->getTraitUses();

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
                    [
                        'startLine' => $constConst->getStartLine(),
                        'endLine' => $constConst->getEndLine(),
                    ]
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

    /**
     * @see Enum::__construct()
     * @see Enum::fromValue()
     */
    protected function refactorNewOrFromValue(New_|StaticCall $node): ?Node
    {
        $class = $node->class;
        if ($class instanceof Name && $this->inConfiguredClasses($class)) {
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

    protected function refactorClassConstFetch(ClassConstFetch $node, Scope $scope): ?Node
    {
        if (! $this->inConfiguredClasses($node)) {
            return null;
        }

        return null;
    }

    /** @see Enum::is() */
    protected function refactorIs(MethodCall|NullsafeMethodCall $node, Scope $scope): ?Node
    {
        $args = $node->args;
        if (isset($args[0]) && $args[0] instanceof Arg) {
            $arg = $args[0];
            $right = $arg->value;

            $var = $node->var;
            $left = $this->willBeEnumInstance($right, $scope)
                ? $var
                : $this->nodeFactory->createPropertyFetch($var, 'value');

            return new Identical($left, $right);
        }

        return null;
    }

    protected function willBeEnumInstance(Expr $expr, Scope $scope): bool
    {
        if ($expr instanceof ClassConstFetch && $this->inConfiguredClasses($expr->class)) {
            return true;
        }

        return $this->inConfiguredClasses($expr);
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

    /** @see Enum::$key */
    protected function refactorKey(PropertyFetch $node): ?Node
    {
        return $this->nodeFactory->createPropertyFetch($node->var, 'name');
    }

    protected function refactorMatch(Match_ $match, Scope $scope): ?Node
    {
        $cond = $match->cond;
        if ($cond instanceof PropertyFetch && $this->inConfiguredClasses($cond->var)) {
            foreach ($match->arms as $arm) {
                if ($arm->conds === null) {
                    continue;
                }

                foreach ($arm->conds as $armCond) {
                    if (! $armCond instanceof ClassConstFetch || ! $this->inConfiguredClasses($armCond->class)) {
                        // Arms must be exclusively enums
                        return null;
                    }
                }
            }

            return new Match_($cond->var, $match->arms, $match->getAttributes());
        }

        $condType = $scope->getType($cond);
        if ($this->isPossibleEnumValueType($condType)) {
            $arms = [];
            foreach ($match->arms as $arm) {
                $arms[] = $arm->conds === null
                    ? $arm
                    : new MatchArm(
                        array_map([$this, 'convertClassConstFetchOrNot'], $arm->conds),
                        $arm->body,
                        $arm->getAttributes(),
                    );
            }

            return new Match_($cond, $arms, $match->getAttributes());
        }

        return null;
    }

    protected function refactorArrayItem(ArrayItem $node): ?Node
    {
        $key = $node->key;
        $convertedKey = $this->convertClassConstFetch($key);

        if ($convertedKey) {
            return new ArrayItem(
                $node->value,
                $convertedKey,
                $node->byRef,
                $node->getAttributes(),
                $node->unpack,
            );
        }

        return null;
    }

    protected function convertClassConstFetch(?Expr $expr): ?PropertyFetch
    {
        if ($expr instanceof ClassConstFetch && $this->inConfiguredClasses($expr->class)) {
            return $this->nodeFactory->createPropertyFetch($expr, 'value');
        }

        return null;
    }

    public function isPossibleEnumValueType(Type $condType): bool
    {
        return $condType->isString()->yes() || $condType->isInteger()->yes();
    }

    protected function convertClassConstFetchOrNot(?Expr $expr): ?Expr
    {
        if ($expr instanceof ClassConstFetch && $this->inConfiguredClasses($expr->class)) {
            return $this->nodeFactory->createPropertyFetch($expr, 'value');
        }

        return $expr;
    }

    protected function refactorBinaryOp(BinaryOp $binaryOp, Scope $scope): ?Node
    {
        $left = $binaryOp->left;
        $convertedLeft = $this->convertClassConstFetch($left);

        $right = $binaryOp->right;
        $convertedRight = $this->convertClassConstFetch($right);

        // It may be valid to use an Enum in comparison with unknown values.
        // However, if we know the other side is a string or int, we can safely convert.
        $isComparison = $binaryOp instanceof Equal
            || $binaryOp instanceof Identical
            || $binaryOp instanceof NotEqual
            || $binaryOp instanceof NotIdentical;
        if ($isComparison) {
            if ($convertedLeft && $convertedRight) {
                // Maybe evaluate for truthiness and replace with static value?
                return null;
            }

            $isStringOrInt = function (Expr $expr) use ($scope): bool {
                return $this->isPossibleEnumValueType($scope->getType($expr));
            };

            if ($convertedLeft && $isStringOrInt($right)) {
                return new $binaryOp($convertedLeft, $right, $binaryOp->getAttributes());
            }

            if ($convertedRight && $isStringOrInt($left)) {
                return new $binaryOp($left, $convertedRight, $binaryOp->getAttributes());
            }

            return null;
        }

        // All other operators only make sense with the underlying values of enums
        // ?? or ?: enums will never be null or falsy, result is likely not used as an enum
        // arithmetic, bitwise, comparison, logical, or string operators do not support enums

        if ($convertedLeft || $convertedRight) {
            return new $binaryOp(
                $convertedLeft ?? $left,
                $convertedRight ?? $right,
                $binaryOp->getAttributes(),
            );
        }

        return null;
    }

    protected function refactorAssign(Assign|AssignOp|AssignRef $assign): ?Node
    {
        $convertedExpr = $this->convertClassConstFetch($assign->expr);
        if ($convertedExpr) {
            return new $assign($assign->var, $convertedExpr, $assign->getAttributes());
        }

        return null;
    }
}
