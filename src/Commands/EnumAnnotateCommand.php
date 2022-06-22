<?php declare(strict_types=1);

namespace BenSampo\Enum\Commands;

use ReflectionClass;
use BenSampo\Enum\Enum;
use Laminas\Code\Generator\DocBlockGenerator;
use Laminas\Code\Reflection\DocBlockReflection;
use Laminas\Code\Generator\DocBlock\Tag\MethodTag;
use Laminas\Code\Generator\DocBlock\Tag\TagInterface;

class EnumAnnotateCommand extends AbstractAnnotationCommand
{
    const DEFAULT_SCAN_FOLDER = 'Enums';
    const PARENT_CLASS = Enum::class;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'enum:annotate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate DocBlock annotations for enum classes';

    /**
     * Apply annotations to a reflected class.
     */
    protected function annotate(ReflectionClass $reflectionClass): void
    {
        $docBlock = DocBlockGenerator::fromArray([]);

        if ($reflectionClass->getDocComment()) {
            $docBlock->setShortDescription(
                DocBlockGenerator::fromReflection(new DocBlockReflection($reflectionClass))
                    ->getShortDescription()
            );
        }

        $this->updateClassDocblock($reflectionClass, $this->getDocBlock($reflectionClass));
    }

    protected function getDocblockTags(array $originalTags, ReflectionClass $reflectionClass): array
    {
        $constants = $reflectionClass->getConstants();

        $existingTags = array_filter($originalTags, fn (TagInterface $tag): bool =>
            ! $tag instanceof MethodTag
            || ! in_array($tag->getMethodName(), array_keys($constants), true));

        return collect($constants)
            ->map(fn (mixed $value, string $constantName): MethodTag => new MethodTag($constantName, ['static'], null, true))
            ->merge($existingTags)
            ->toArray();
    }

    protected function searchDirectory(): string
    {
        return $this->option('folder') ?? app_path(self::DEFAULT_SCAN_FOLDER);
    }
}
