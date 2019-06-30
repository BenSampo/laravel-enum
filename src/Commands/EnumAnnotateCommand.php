<?php

namespace BenSampo\Enum\Commands;

use BenSampo\Enum\Enum;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\Static_;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

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
    protected $description = 'Generate annotations for enum classes';

    /**
     * Apply annotations to a reflected class
     *
     * @param ReflectionClass $reflectionClass
     *
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function annotate(ReflectionClass $reflectionClass)
    {
        $factory = DocBlockFactory::createInstance();

        $existingDocBlock = null;

        if (strlen($reflectionClass->getDocComment()) !== 0) {
            $existingDocBlock = $docBlock = $factory->create($reflectionClass);
            $existingDocBlock = $this->removeExistingStaticMethods($existingDocBlock, $reflectionClass->getConstants());
        }

        $newDocblock = $this->mergeTagsIntoDocblock(
            $this->getStaticEnumMethods($reflectionClass->getConstants()),
            $existingDocBlock
        );

        $this->updateClassDocblock($reflectionClass, $newDocblock);
    }

    private function removeExistingStaticMethods(DocBlock $docBlock, array $constants): DocBlock
    {
        $existingMethods = $docBlock->getTagsByName('method');

        foreach ($constants as $name => $value) {
            /** @var DocBlock\Tags\Method $method */
            foreach ($existingMethods as $method) {
                if ($method->getName() === $name) {
                    $docBlock->removeTag($method);
                }
            }
        }

        return $docBlock;
    }

    private function getStaticEnumMethods(array $constants): array
    {
        return array_map(function (string $name) {
            return new DocBlock\Tags\Method($name, [], new Static_(), true);
        }, array_keys($constants));
    }

    protected function getClassFinder(): Finder
    {
        $finder = new Finder();
        $scanPath = $this->option('folder') ?? app_path(self::DEFAULT_SCAN_FOLDER);

        return $finder->files()->in($scanPath)->name('*.php');
    }
}
