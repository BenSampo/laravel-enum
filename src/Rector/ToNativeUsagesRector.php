<?php declare(strict_types=1);

namespace BenSampo\Enum\Rector;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Tests\Enums\UserType;
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
use PhpParser\Node\Expr\BinaryOp\Coalesce;
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
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\MatchArm;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\Encapsed;
use PhpParser\Node\Scalar\EncapsedStringPart;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\VariadicPlaceholder;
use PHPStan\Type\ObjectType;
use Rector\StaticTypeMapper\ValueObject\Type\FullyQualifiedObjectType;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \BenSampo\Enum\Tests\Rector\ToNativeRectorUsagesTest
 */
class ToNativeUsagesRector extends ToNativeRector
{
    public const CONVERTED_COMPARISON = ToNativeUsagesRector::class . '@converted-comparison';
    public const CONVERTED_INSTANTIATION = ToNativeUsagesRector::class . '@converted-instantiation';

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
                    UserType::class,
                ],
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [
            New_::class,
            ArrayItem::class,
            ArrayDimFetch::class,
            BinaryOp::class,
            Ternary::class,
            Cast::class,
            Encapsed::class,
            Assign::class,
            AssignOp::class,
            AssignRef::class,
            ArrowFunction::class,
            Return_::class,
            Param::class,
            Match_::class,
            CallLike::class,
            PropertyFetch::class,
        ];
    }

    public function refactor(Node $node): ?Node
    {
        $this->classes ??= [new ObjectType(Enum::class)];

        if ($node instanceof ArrayItem) {
            return $this->refactorArrayItem($node);
        }

        if ($node instanceof ArrayDimFetch) {
            return $this->refactorArrayDimFetch($node);
        }

        if ($node instanceof BinaryOp) {
            return $this->refactorBinaryOp($node);
        }

        if ($node instanceof Ternary) {
            return $this->refactorTernary($node);
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

        if ($node instanceof Param) {
            return $this->refactorParam($node);
        }

        if ($node instanceof Match_) {
            return $this->refactorMatch($node);
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

                if ($this->isName($node->name, 'getRandomInstance')) {
                    return $this->refactorGetRandomInstance($node);
                }

                return $this->refactorMaybeMagicStaticCall($node);
            }

            if (
                ($node instanceof MethodCall || $node instanceof NullsafeMethodCall)
                && $this->inConfiguredClasses($node->var)
            ) {
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

                if ($this->isName($node->name, '__toString')) {
                    return $this->refactorMagicToString($node);
                }
            }

            return $this->refactorCall($node);
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

    /**
     * @see Enum::__construct()
     * @see Enum::fromValue()
     */
    protected function refactorNewOrFromValue(New_|StaticCall $node): ?Node
    {
        $class = $node->class;
        if ($class instanceof Name) {
            $classString = $class->toString();

            if ($node->isFirstClassCallable()) {
                return new StaticCall($class, 'from', [new VariadicPlaceholder()]);
            }

            $args = $node->args;
            if (isset($args[0])) {
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

                return new StaticCall($class, 'from', [new Arg($argValue)]);
            }
        }

        return null;
    }

    /** @see Enum::getInstances() */
    protected function refactorGetInstances(StaticCall $node): ?StaticCall
    {
        $class = $node->class;
        if ($class instanceof Name) {
            return new StaticCall($class, 'cases');
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

                return new FuncCall(
                    new Name('array_map'),
                    [
                        new Arg(
                            new ArrowFunction([
                                'static' => true,
                                'params' => [new Param($paramVariable, null, $class)],
                                'returnType' => 'string',
                                'expr' => new PropertyFetch($paramVariable, 'name'),
                            ])
                        ),
                        new Arg(
                            new StaticCall($class, 'cases')
                        ),
                    ]
                );
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

                return new FuncCall(
                    new Name('array_map'),
                    [
                        new Arg(
                            new ArrowFunction([
                                'static' => true,
                                'params' => [new Param($paramVariable, null, $class)],
                                'expr' => new PropertyFetch($paramVariable, 'value'),
                            ])
                        ),
                        new Arg(
                            new StaticCall($class, 'cases')
                        ),
                    ],
                );
            }
        }

        return null;
    }

    /** @see Enum::getRandomInstance() */
    protected function refactorGetRandomInstance(StaticCall $staticCall): ?Node
    {
        return new MethodCall(
            new FuncCall(new Name('fake')),
            'randomElement',
            [new Arg(new StaticCall($staticCall->class, 'cases'))]
        );
    }

    /**
     * @see Enum::__callStatic()
     * @see Enum::__call()
     */
    protected function refactorMaybeMagicStaticCall(StaticCall $node): ?Node
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

    /** @see Enum::is() */
    protected function refactorIs(MethodCall|NullsafeMethodCall $call): ?Node
    {
        if ($call->isFirstClassCallable()) {
            $param = new Variable('value');

            return new ArrowFunction([
                'params' => [new Param($param, null, 'mixed')],
                'returnType' => 'bool',
                'expr' => new Identical($call->var, $param, [self::CONVERTED_COMPARISON => true]),
            ]);
        }

        $args = $call->getArgs();
        if (isset($args[0])) {
            $arg = $args[0];
            $right = $arg->value;

            $var = $call->var;
            $left = $this->willBeEnumInstance($right)
                ? $var
                : new PropertyFetch($var, 'value');

            return new Identical($left, $right, [self::CONVERTED_COMPARISON => true]);
        }

        return null;
    }

    protected function willBeEnumInstance(Expr $expr): bool
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

            return new NotIdentical($node->var, $arg->value, [self::CONVERTED_COMPARISON => true]);
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
                [self::CONVERTED_COMPARISON => true],
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
                    [self::CONVERTED_COMPARISON => true],
                )
            );
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

    /** @see Enum::$key */
    protected function refactorKey(PropertyFetch $node): ?Node
    {
        return new PropertyFetch($node->var, 'name');
    }

    protected function refactorMatch(Match_ $match): ?Node
    {
        $cond = $match->cond;
        if (($cond instanceof PropertyFetch || $cond instanceof NullsafePropertyFetch)
            && $this->inConfiguredClasses($cond->var)
        ) {
            $var = $cond->var;
            $varType = $this->getType($var);

            $armsAreExclusivelyEnumsOrNull = true;
            foreach ($match->arms as $arm) {
                if ($arm->conds === null) {
                    continue;
                }

                foreach ($arm->conds as $armCond) {
                    $isEnum = $varType->equals($this->getType($armCond))
                        || ($armCond instanceof ClassConstFetch && $this->inConfiguredClasses($armCond->class));
                    $isNull = $this->getType($armCond)->isNull()->yes();

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
        $convertedKey = $this->convertConstToValueFetch($key);

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
        if (! $expr) {
            return null;
        }

        $constValueFetch = $this->convertConstToValueFetch($expr);
        if ($constValueFetch) {
            return $constValueFetch;
        }

        if ($this->inConfiguredClasses($expr)) {
            return $this->createValueFetch($expr, $this->nodeTypeResolver->isNullableType($expr));
        }

        return null;
    }

    protected function convertConstToValueFetch(?Expr $expr): ?Expr
    {
        if (! $expr || $expr->hasAttribute(self::CONVERTED_INSTANTIATION)) {
            return null;
        }

        if (
            $expr instanceof ClassConstFetch
            && $this->inConfiguredClasses($expr->class)
            && $expr->name->name !== 'class'
        ) {
            return $this->createValueFetch($expr, false);
        }

        return null;
    }

    protected function refactorBinaryOp(BinaryOp $binaryOp): ?Node
    {
        if ($binaryOp->hasAttribute(self::CONVERTED_COMPARISON)) {
            return null;
        }

        $left = $binaryOp->left;
        $right = $binaryOp->right;

        if ($binaryOp instanceof Coalesce) {
            // ->isString()->yes() could be string or string|null, but since it is one the left side of ?? we assume the latter
            if ($this->getType($left)->isString()->yes() && ! $this->willBeEnumInstance($left)) {
                $convertedRight = $this->convertToValueFetch($right);
                if ($convertedRight) {
                    return new Coalesce($left, $convertedRight, $binaryOp->getAttributes());
                }
            }
            if ($this->getType($right)->isString()->yes() && ! $this->willBeEnumInstance($right)) {
                $convertedLeft = $this->convertToValueFetch($left);
                if ($convertedLeft) {
                    return new Coalesce($convertedLeft, $right, $binaryOp->getAttributes());
                }
            }

            $convertedLeft = $this->convertConstToValueFetch($left);
            $convertedRight = $this->convertConstToValueFetch($right);

            if ($convertedLeft || $convertedRight) {
                return new Coalesce(
                    $convertedLeft ?? $left,
                    $convertedRight ?? $right,
                    $binaryOp->getAttributes(),
                );
            }

            return null;
        }

        if ($binaryOp instanceof Equal
            || $binaryOp instanceof Identical
            || $binaryOp instanceof NotEqual
            || $binaryOp instanceof NotIdentical
        ) {
            // Comparison of two class constants of the same class will become enum comparison
            if (
                ($left instanceof ClassConstFetch && $right instanceof ClassConstFetch)
                && ($left->class instanceof Name && $right->class instanceof Name)
                && ($left->class->toString() === $right->class->toString())
            ) {
                return null;
            }

            // If either side of the comparison is an enum, do not convert
            if ($this->inConfiguredClasses($left) || $this->inConfiguredClasses($right)) {
                return null;
            }

            $convertedLeft = $this->convertConstToValueFetch($left);
            $convertedRight = $this->convertConstToValueFetch($right);

            if ($convertedLeft || $convertedRight) {
                return new $binaryOp(
                    $convertedLeft ?? $left,
                    $convertedRight ?? $right,
                    $binaryOp->getAttributes(),
                );
            }

            return null;
        }

        // The remaining operators are: arithmetic, bitwise, comparison, logical, string.
        // They do not support enums and only work with the underlying values.
        $convertedLeft = $this->convertToValueFetch($left);
        $convertedRight = $this->convertToValueFetch($right);
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
        $convertedExpr = $this->convertConstToValueFetch($assign->expr);
        $var = $assign->var;
        if ($convertedExpr && ! $this->inConfiguredClasses($var)) {
            return new $assign($var, $convertedExpr, $assign->getAttributes());
        }

        return null;
    }

    protected function refactorCall(CallLike $call): ?CallLike
    {
        // At this point, we know the call is neither new'ing up a Bensampo\Enum\Enum,
        // nor is it statically or dynamically calling any of its methods which require
        // special conversion rules. Thus, we are safe to transform any const fetches to values.

        if ($call->isFirstClassCallable()) {
            return null;
        }

        $args = [];
        foreach ($call->getArgs() as $arg) {
            $args[] = new Arg(
                $this->convertConstToValueFetch($arg->value) ?? $arg->value,
                $arg->byRef,
                $arg->unpack,
                $arg->getAttributes(),
                $arg->name,
            );
        }

        if ($call instanceof FuncCall && ! $call->hasAttribute(self::CONVERTED_COMPARISON)) {
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
        if (! $expr || $expr->hasAttribute(self::CONVERTED_INSTANTIATION)) {
            return null;
        }

        $convertedExpr = $this->convertConstToValueFetch($expr);
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

    protected function createValueFetch(Expr $expr, bool $isNullable): NullsafePropertyFetch|PropertyFetch
    {
        return $isNullable
            ? new NullsafePropertyFetch($expr, 'value')
            : new PropertyFetch($expr, 'value');
    }

    protected function refactorArrowFunction(ArrowFunction $arrowFunction): ?ArrowFunction
    {
        $convertedExpr = $this->convertConstToValueFetch($arrowFunction->expr);
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

    protected function refactorParam(Param $param): ?Param
    {
        $convertedDefault = $this->convertConstToValueFetch($param->default);
        if ($convertedDefault) {
            return new Param(
                $param->var,
                $convertedDefault,
                $param->type,
                $param->byRef,
                $param->variadic,
                $param->getAttributes(),
                $param->flags,
                $param->attrGroups,
            );
        }

        return null;
    }

    protected function refactorTernary(Ternary $ternary): ?Node
    {
        $if = $ternary->if;
        $convertedIf = $this->convertConstToValueFetch($if);

        $else = $ternary->else;
        $convertedElse = $this->convertConstToValueFetch($else);

        if ($convertedIf || $convertedElse) {
            return new Ternary(
                $ternary->cond,
                $convertedIf ?? $if,
                $convertedElse ?? $else,
                $ternary->getAttributes(),
            );
        }

        return null;
    }
}
