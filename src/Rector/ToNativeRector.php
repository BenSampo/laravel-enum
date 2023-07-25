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
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\EnumCase;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDocParser\Ast\PhpDoc\ExtendsTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueNode;
use PHPStan\Type\ObjectType;
use Rector\BetterPhpDocParser\Printer\PhpDocInfoPrinter;
use Rector\Core\Contract\Rector\AllowEmptyConfigurableRectorInterface;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractScopeAwareRector;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\StaticTypeMapper\ValueObject\Type\FullyQualifiedObjectType;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/** @see \BenSampo\Enum\Tests\Rector\ToNativeRectorTest */
class ToNativeRector extends AbstractScopeAwareRector implements ConfigurableRuleInterface, ConfigurableRectorInterface, AllowEmptyConfigurableRectorInterface
{
    /** @var array<ObjectType> */
    protected array $classes;

    public function __construct(
        protected PhpDocInfoPrinter $phpDocInfoPrinter,
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

    public function getNodeTypes(): array
    {
        return [
            Class_::class,
            New_::class,
            ClassConstFetch::class,
            ArrayItem::class,
            StaticCall::class,
            MethodCall::class,
            NullsafeMethodCall::class,
            PropertyFetch::class,
        ];
    }

    /** TODO scope necessary? */
    public function refactorWithScope(Node $node, Scope $scope): ?Node
    {
        $this->classes ??= [new ObjectType(Enum::class)];

        foreach ($this->classes as $class) {
            if ($this->usesConfiguredClass($node, $class)) {
                return $this->refactorNode($node, $scope);
            }
        }

        return null;
    }

    protected function usesConfiguredClass(Node $node, ObjectType $class): bool
    {
        if ($node instanceof Class_) {
            return $this->isObjectType($node, $class);
        }

        if ($node instanceof ArrayItem) {
            $key = $node->key;

            return $key instanceof ClassConstFetch
                && $this->isObjectType($key->class, $class);
        }

        if ($node instanceof New_ || $node instanceof ClassConstFetch || $node instanceof StaticCall) {
            return $this->isObjectType($node->class, $class);
        }

        if ($node instanceof MethodCall || $node instanceof NullsafeMethodCall || $node instanceof PropertyFetch) {
            return $this->isObjectType($node->var, $class);
        }

        return false;
    }

    protected function refactorNode(Node $node, Scope $scope): ?Node
    {
        if ($node instanceof Class_) {
            return $this->refactorClass($node);
        }

        if ($node instanceof ArrayItem) {
            return $this->refactorArrayItem($node);
        }

        if ($node instanceof New_) {
            return $this->refactorNewOrFromValue($node);
        }

        if ($node instanceof ClassConstFetch) {
            return $this->refactorClassConstFetch($node, $scope);
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

        if ($node instanceof PropertyFetch) {
            if ($this->isName($node->name, 'key')) {
                return $this->refactorKey($node);
            }
        }

        return null;
    }

    /** @see \Rector\Php81\NodeFactory\EnumFactory */
    protected function refactorClass(Class_ $class): ?Node
    {
        $enum = new Enum_(
            $this->nodeNameResolver->getShortName($class),
            [],
            ['startLine' => $class->getStartLine(), 'endLine' => $class->getEndLine()]
        );
        $enum->namespacedName = $class->namespacedName;

        $docComment = $class->getDocComment();
        if ($docComment) {
            $phpDocInfo = $this->phpDocInfoFactory->createFromNode($class);
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

    protected function refactorArrayItem(ArrayItem $node): ?Node
    {
        $key = $node->key;
        if ($key instanceof ClassConstFetch) {
            return new ArrayItem(
                $node->value,
                $this->nodeFactory->createPropertyFetch($key, 'value'),
                $node->byRef,
                $node->getAttributes(),
                $node->unpack,
            );
        }

        return null;
    }
}
