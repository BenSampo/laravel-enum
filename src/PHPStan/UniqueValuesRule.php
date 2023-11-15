<?php declare(strict_types=1);

namespace BenSampo\Enum\PHPStan;

use BenSampo\Enum\Enum;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/** @implements Rule<InClassNode> */
final class UniqueValuesRule implements Rule
{
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        assert($node instanceof InClassNode);

        $reflection = $node->getClassReflection();
        if (! $reflection->isSubclassOf(Enum::class)) {
            return [];
        }

        $constants = [];
        foreach ($reflection->getNativeReflection()->getReflectionConstants() as $constant) {
            $constants[$constant->name] = $constant->getValue();
        }

        if (count($constants) !== count(array_unique($constants))) {
            $fqcn = $reflection->getName();
            $constantsString = json_encode($constants);

            return [
                RuleErrorBuilder::message("Enum class {$fqcn} contains constants with duplicate values: {$constantsString}.")
                    ->build(),
            ];
        }

        return [];
    }
}
