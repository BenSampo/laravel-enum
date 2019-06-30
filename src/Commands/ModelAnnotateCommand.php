<?php

namespace BenSampo\Enum\Commands;

use BenSampo\Enum\Docblock\EnumPropertyType;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

class ModelAnnotateCommand extends AbstractAnnotationCommand
{
    const PARENT_CLASS = Model::class;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'enum:annotate-model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate annotations for models that have enums';

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
        if (!$reflectionClass->hasMethod('hasEnumCast')) {
            return;
        }

        $casts = $reflectionClass->getDefaultProperties()['enumCasts'] ?? [];
        $factory = DocBlockFactory::createInstance();

        $existingDocBlock = null;

        if (strlen($reflectionClass->getDocComment()) !== 0) {
            $existingDocBlock = $docBlock = $factory->create($reflectionClass);
            $existingDocBlock = $this->removeExistingCastPropertyTags($existingDocBlock, $casts);
        }

        $newDocblock = $this->mergeTagsIntoDocblock($this->getCastPropertyTags($casts), $existingDocBlock);

        $this->updateClassDocblock($reflectionClass, $newDocblock);
    }

    protected function getClassFinder(): Finder
    {
        $finder = new Finder();

        if (!$this->option('folder')) {
            return $finder->files()->in(app_path())->depth('==0')->name('*.php');
        }

        return $finder->files()->in($this->option('folder'))->name('*.php');
    }

    private function removeExistingCastPropertyTags(DocBlock $docBlock, array $casts): DocBlock
    {
        $existingProperties = $docBlock->getTagsByName('property');

        foreach ($casts as $property => $className) {
            /** @var DocBlock\Tags\Property $property */
            foreach ($existingProperties as $property) {
                if ($property->getVariableName() == $property) {
                    $docBlock->removeTag($property);
                }
            }
        }

        return $docBlock;
    }

    private function getCastPropertyTags(array $enumCasts): array
    {
        $casts = [];

        foreach ($enumCasts as $property => $className) {
            $casts[] = new DocBlock\Tags\Property($property, new EnumPropertyType($className));
        }

        return $casts;
    }
}
