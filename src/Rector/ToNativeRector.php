<?php declare(strict_types=1);

namespace BenSampo\Enum\Rector;

use PhpParser\Node;
use PHPStan\Type\ObjectType;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;

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
