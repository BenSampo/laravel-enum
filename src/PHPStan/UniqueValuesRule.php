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

        $duplicateConstants = [];
        foreach ($constants as $name => $value) {
            $constantsWithValue = array_filter($constants, fn (mixed $v): bool => $v === $value);
            if (count($constantsWithValue) > 1) {
                $duplicateConstants []= array_keys($constantsWithValue);
            }
        }
        $duplicateConstants = array_unique($duplicateConstants);

        if (count($duplicateConstants) > 0) {
            $fqcn = $reflection->getName();
            $constantsString = json_encode($duplicateConstants);

            return [
                RuleErrorBuilder::message("Enum class {$fqcn} contains constants with duplicate values: {$constantsString}.")
                    ->build(),
            ];
        }

        return [];
    }
}
