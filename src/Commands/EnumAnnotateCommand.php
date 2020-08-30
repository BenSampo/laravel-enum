<?php

namespace BenSampo\Enum\Commands;

use ReflectionClass;
use BenSampo\Enum\Enum;
use Symfony\Component\Finder\Finder;
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
     * Apply annotations to a reflected class
     *
     * @param  \ReflectionClass  $reflectionClass
     * @return void
     */
    protected function annotate(ReflectionClass $reflectionClass)
    {
        $docBlock = DocBlockGenerator::fromArray([]);
        $originalDocBlock =  null;

        if (strlen($reflectionClass->getDocComment()) !== 0) {
            $originalDocBlock = DocBlockGenerator::fromReflection(new DocBlockReflection($reflectionClass));
            $docBlock->setShortDescription($originalDocBlock->getShortDescription());
        }

        $this->updateClassDocblock($reflectionClass, $this->getDocBlock($reflectionClass));
    }

    protected function getDocblockTags(array $originalTags, ReflectionClass $reflectionClass): array
    {
        $constants = $reflectionClass->getConstants();

        $existingTags = array_filter($originalTags, function (TagInterface $tag) use ($constants) {
            return !$tag instanceof MethodTag || !in_array($tag->getMethodName(), array_keys($constants), true);
        });

        return collect($constants)
            ->map(function ($value, $constantName) {
                return new MethodTag($constantName, ['static'], null, true);
            })
            ->merge($existingTags)
            ->toArray();
    }

    protected function getClassFinder(): Finder
    {
        $finder = new Finder();
        $scanPath = $this->option('folder') ?? app_path(self::DEFAULT_SCAN_FOLDER);

        return $finder->files()->in($scanPath)->name('*.php');
    }
}
