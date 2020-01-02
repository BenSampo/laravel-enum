<?php

namespace BenSampo\Enum\Commands;

use ReflectionClass;
use Symfony\Component\Finder\Finder;
use Illuminate\Database\Eloquent\Model;
use Laminas\Code\Generator\DocBlock\Tag\PropertyTag;
use Laminas\Code\Generator\DocBlock\Tag\TagInterface;

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
    protected $description = 'Generate DocBlock annotations for models that have enums';

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

        $this->updateClassDocblock($reflectionClass, $this->getDocBlock($reflectionClass));
    }

    protected function getClassFinder(): Finder
    {
        $finder = new Finder();

        if (!$this->option('folder')) {
            return $finder->files()->in(app_path())->depth('==0')->name('*.php');
        }

        return $finder->files()->in($this->option('folder'))->name('*.php');
    }

    protected function getDocblockTags(array $originalTags, ReflectionClass $reflectionClass): array
    {
        $casts = $reflectionClass->getDefaultProperties()['enumCasts'] ?? [];

        $existingTags = array_filter($originalTags, function (TagInterface $tag) use ($casts) {
            return !$tag instanceof PropertyTag || !in_array($tag->getPropertyName(), array_keys($casts), true);
        });

        return collect($casts)
            ->map(function ($className, $propertyName) {
                return new PropertyTag($propertyName, [sprintf('\%s', $className), 'null']);
            })
            ->merge($existingTags)
            ->toArray();
    }
}
