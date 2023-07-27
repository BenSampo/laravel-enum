<?php declare(strict_types=1);

namespace BenSampo\Enum\Rector;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Tests\Enums\UserType;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
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
use PhpParser\Node\Expr\CallLike;
use PhpParser\Node\Expr\Cast;
use PhpParser\Node\Expr\Cast\String_;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Match_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\MatchArm;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\Encapsed;
use PhpParser\Node\Scalar\EncapsedStringPart;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\EnumCase;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\VariadicPlaceholder;
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
use Rector\Php81\Rector\Array_\FirstClassCallableRector;
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

    public const CONVERTED_IN_ARRAY = ToNativeRector::class . '@converted-in-array';
    public const CONVERTED_INSTANTIATION = ToNativeRector::class . '@converted-instantiation';

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
                ArrayItem::class,
                ArrayDimFetch::class,
                BinaryOp::class,
                Cast::class,
                Encapsed::class,
                Assign::class,
                AssignOp::class,
                AssignRef::class,
                ArrowFunction::class,
                Return_::class,
                Match_::class,
                CallLike::class,
                PropertyFetch::class,
            ],
            ToNativeRector::IMPLEMENTATION => [
                Class_::class,
            ],
        };
    }

    // TODO replace Scope by $this->getType()?
    public function refactorWithScope(Node $node, Scope $scope): ?Node
    {
        $this->classes ??= [new ObjectType(Enum::class)];

        if ($node instanceof Class_) {
            return $this->refactorClass($node);
        }

        if ($node instanceof ArrayItem) {
            return $this->refactorArrayItem($node);
        }

        if ($node instanceof ArrayDimFetch) {
            return $this->refactorArrayDimFetch($node);
        }

        if ($node instanceof BinaryOp) {
            return $this->refactorBinaryOp($node, $scope);
        }

        if ($node instanceof Cast) {
            return $this->refactorCast($node);
        }

        if ($node instanceof Encapsed) {
            return $this->refactorEncapsed($node);
        }

        if ($node instanceof Assign || $node instanceof AssignOp || $node instanceof AssignRef) {
            return $this->refactorAssign($node);
        }

        if ($node instanceof ArrowFunction) {
            return $this->refactorArrowFunction($node);
        }

        if ($node instanceof Return_) {
            return $this->refactorReturn($node);
        }

        if ($node instanceof Match_) {
            return $this->refactorMatch($node, $scope);
        }

        if ($node instanceof CallLike) {
            if ($node instanceof New_ && $this->inConfiguredClasses($node->class)) {
                return $this->refactorNewOrFromValue($node);
            }

            if ($node instanceof StaticCall && $this->inConfiguredClasses($node->class)) {
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

                // TODO getRandomInstance

                return $this->refactorMagicStaticCall($node);
            }

            if (
                ($node instanceof MethodCall || $node instanceof NullsafeMethodCall)
                && $this->inConfiguredClasses($node->var)
            ) {
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

                if ($this->isName($node->name, '__toString')) {
                    return $this->refactorMagicToString($node);
                }
            }

            return $this->refactorCall($node, $scope);
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
                        return $this->createEnumCaseAccess($class, $argValueName->name);
                    }
                }

                return $this->nodeFactory->createStaticCall($classString, 'from', [$argValue]);
            }
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

            return new FuncCall(
                new Name('in_array'),
                [new Arg($node->var), $arg],
                [self::CONVERTED_IN_ARRAY => true],
            );
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
                new FuncCall(
                    new Name('in_array'),
                    [new Arg($node->var), $arg],
                    [self::CONVERTED_IN_ARRAY => true],
                )
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
                return $this->createEnumCaseAccess($class, $constName);
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
        if (($cond instanceof PropertyFetch || $cond instanceof NullsafePropertyFetch)
            && $this->inConfiguredClasses($cond->var)
        ) {
            $var = $cond->var;
            $varType = $scope->getType($var);

            $armsAreExclusivelyEnumsOrNull = true;
            foreach ($match->arms as $arm) {
                if ($arm->conds === null) {
                    continue;
                }

                foreach ($arm->conds as $armCond) {
                    $isEnum = $varType->equals($this->getType($armCond))
                        || ($armCond instanceof ClassConstFetch && $this->inConfiguredClasses($armCond->class));
                    $isNull = $scope->getType($armCond)->isNull()->yes();

                    if (! $isEnum && ! $isNull) {
                        $armsAreExclusivelyEnumsOrNull = false;
                    }
                }
            }

            if ($armsAreExclusivelyEnumsOrNull) {
                return new Match_($var, $match->arms, $match->getAttributes());
            }
        }

        if ($this->inConfiguredClasses($cond)) {
            return null;
        }

        $arms = [];
        foreach ($match->arms as $arm) {
            $arms[] = $arm->conds === null
                ? $arm
                : new MatchArm(
                    array_map(fn (Expr $expr) => $this->convertToValueFetch($expr) ?? $expr, $arm->conds),
                    $arm->body,
                    $arm->getAttributes(),
                );
        }

        return new Match_($cond, $arms, $match->getAttributes());
    }

    protected function refactorArrayItem(ArrayItem $node): ?Node
    {
        $key = $node->key;
        $convertedKey = $this->convertToValueFetch($key);

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

    protected function convertToValueFetch(?Expr $expr): ?Expr
    {
        if (! $expr || $expr->hasAttribute(self::CONVERTED_INSTANTIATION)) {
            return null;
        }

        if (
            ($expr instanceof ClassConstFetch && $this->inConfiguredClasses($expr->class))
            || $this->inConfiguredClasses($expr)
        ) {
            return $this->createValueFetch($expr, $this->nodeTypeResolver->isNullableType($expr));
        }

        return null;
    }

    public function isPossibleEnumValueType(Type $condType): bool
    {
        // includes int|string|null
        return $condType->isScalar()->yes();
    }

    protected function refactorBinaryOp(BinaryOp $binaryOp, Scope $scope): ?Node
    {
        $left = $binaryOp->left;
        $convertedLeft = $this->convertToValueFetch($left);

        $right = $binaryOp->right;
        $convertedRight = $this->convertToValueFetch($right);

        // It may be valid to use an Enum in comparison with unknown values.
        // However, if we know the other side is a string or int, we can safely convert.
        if ($binaryOp instanceof Equal
            || $binaryOp instanceof Identical
            || $binaryOp instanceof NotEqual
            || $binaryOp instanceof NotIdentical
        ) {
            if ($convertedLeft && $convertedRight) {
                // Maybe evaluate for truthiness and replace with static value?
                return null;
            }

            $isPossibleEnumValueType = function (Expr $expr) use ($scope): bool {
                return $this->isPossibleEnumValueType($scope->getType($expr));
            };

            if ($convertedLeft && $isPossibleEnumValueType($right)) {
                return new $binaryOp($convertedLeft, $right, $binaryOp->getAttributes());
            }

            if ($convertedRight && $isPossibleEnumValueType($left)) {
                return new $binaryOp($left, $convertedRight, $binaryOp->getAttributes());
            }

            // TODO maybe convert either way? e.g. $foo?->var === UserType::Admin
            return null;
        }

        // All other operators only make sense with the underlying values of enums
        // arithmetic, bitwise, comparison, logical, or string operators do not support enums
        // ?? or ?: enums will never be null or falsy, result is likely not used as an enum

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
        $convertedExpr = $this->convertToValueFetch($assign->expr);
        $var = $assign->var;
        if ($convertedExpr && ! $this->inConfiguredClasses($var)) {
            return new $assign($var, $convertedExpr, $assign->getAttributes());
        }

        return null;
    }

    protected function refactorCall(CallLike $call, Scope $scope): ?CallLike
    {
        // At this point, we know the call is neither new'ing up a Bensampo\Enum\Enum,
        // nor is it statically or dynamically calling any of its methods which require
        // special conversion rules. Thus, we are safe to transform any const fetches to values.

        if ($call->isFirstClassCallable()) {
            return null;
        }

        $args = [];
        foreach ($call->getArgs() as $arg) {
            if ($arg instanceof VariadicPlaceholder) {
                return null;
            }

            $args[] = new Arg(
                $this->convertToValueFetch($arg->value) ?? $arg->value,
                $arg->byRef,
                $arg->unpack,
                $arg->getAttributes(),
                $arg->name,
            );
        }

        if ($call instanceof FuncCall && ! $call->hasAttribute(self::CONVERTED_IN_ARRAY)) {
            return new FuncCall($call->name, $args, $call->getAttributes());
        }

        if ($call instanceof New_) {
            return new New_($call->class, $args, $call->getAttributes());
        }

        if ($call instanceof MethodCall || $call instanceof NullsafeMethodCall) {
            return new $call($call->var, $call->name, $args, $call->getAttributes());
        }

        if ($call instanceof StaticCall) {
            return new StaticCall($call->class, $call->name, $args, $call->getAttributes());
        }

        return null;
    }

    protected function refactorReturn(Return_ $return): ?Node
    {
        $expr = $return->expr;
        if ($expr->hasAttribute(self::CONVERTED_INSTANTIATION)) {
            return null;
        }

        $convertedExpr = $this->convertToValueFetch($expr);
        if ($convertedExpr) {
            return new Return_($convertedExpr, $return->getAttributes());
        }

        return null;
    }

    protected function refactorArrayDimFetch(ArrayDimFetch $arrayDimFetch): ?Node
    {
        $convertedDim = $this->convertToValueFetch($arrayDimFetch->dim);
        if ($convertedDim) {
            return new ArrayDimFetch($arrayDimFetch->var, $convertedDim, $arrayDimFetch->getAttributes());
        }

        return null;
    }

    protected function refactorEncapsed(Encapsed $encapsed): Encapsed
    {
        $parts = [];
        foreach ($encapsed->parts as $part) {
            if ($part instanceof EncapsedStringPart) {
                $parts[] = $part;
            } else {
                $parts[] = $this->convertToValueFetch($part) ?? $part;
            }
        }

        return new Encapsed($parts, $encapsed->getAttributes());
    }

    protected function refactorCast(Cast $cast): ?Cast
    {
        $convertedExpr = $this->convertToValueFetch($cast->expr);
        if ($convertedExpr) {
            return new $cast($convertedExpr, $cast->getAttributes());
        }

        return null;
    }

    /** @see Enum::__toString() */
    protected function refactorMagicToString(MethodCall|NullsafeMethodCall $node): Cast
    {
        return new String_(
            $this->createValueFetch($node->var, $node instanceof NullsafeMethodCall)
        );
    }

    protected function createValueFetch(Expr $expr, bool $isNullable): NullsafePropertyFetch|PropertyFetch
    {
        return $isNullable
            ? new NullsafePropertyFetch($expr, 'value')
            : new PropertyFetch($expr, 'value');
    }

    protected function refactorArrowFunction(ArrowFunction $arrowFunction): ?ArrowFunction
    {
        $convertedExpr = $this->convertToValueFetch($arrowFunction->expr);
        if ($convertedExpr) {
            return new ArrowFunction(
                [
                    'static' => $arrowFunction->static,
                    'byRef' => $arrowFunction->byRef,
                    'params' => $arrowFunction->params,
                    'returnType' => $arrowFunction->returnType,
                    'expr' => $convertedExpr,
                    'attrGroups' => $arrowFunction->attrGroups,
                ],
                $arrowFunction->getAttributes(),
            );
        }

        return null;
    }

    protected function createEnumCaseAccess(Name $class, string $constName): ClassConstFetch
    {
        return new ClassConstFetch(
            $class,
            $constName,
            [self::CONVERTED_INSTANTIATION => true],
        );
    }
}
