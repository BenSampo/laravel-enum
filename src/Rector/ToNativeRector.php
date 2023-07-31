<?php declare(strict_types=1);

namespace BenSampo\Enum\Rector;

use PhpParser\Node;
use PHPStan\Type\ObjectType;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;

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
}
