<?php declare(strict_types=1);

namespace BenSampo\Enum\Rector;

use Illuminate\Support\Arr;
use PhpParser\Node;
use PHPStan\Type\ObjectType;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\PhpParser\Node\Value\ValueResolver;
use Rector\Rector\AbstractRector;

/**
 * Conversion of enums and their usages can not be done in a single run of Rector,
 * that would leave the project partially converted.
 * Usages seen after the enum classes have been converted will no longer be transformed.
 *
 * Thus, we split into two rectors that can be run separately.
 */
abstract class ToNativeRector extends AbstractRector implements ConfigurableRectorInterface
{
    /** @var array<ObjectType> */
    protected array $classes;

    public function __construct(
        protected ValueResolver $valueResolver
    ) {}

    /** @param array<class-string> $configuration */
    public function configure(array $configuration): void
    {
        $this->classes = array_map(
            static fn (string $class): ObjectType => new ObjectType($class),
            $configuration,
        );
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

    /** @param array<mixed> $constantValues */
    protected function enumScalarType(array $constantValues): ?string
    {
        if ($constantValues === []) {
            return null;
        }

        // Assume the first constant value has the correct type
        $value = Arr::first($constantValues);
        if (is_string($value)) {
            return 'string';
        }

        if (is_int($value)) {
            return 'int';
        }

        return null;
    }
}
