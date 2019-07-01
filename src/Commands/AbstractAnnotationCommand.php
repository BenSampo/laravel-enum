<?php
declare(strict_types=1);

namespace BenSampo\Enum\Commands;

use BenSampo\Enum\Enum;
use hanneskod\classtools\Iterator\ClassIterator;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use InvalidArgumentException;
use phpDocumentor\Reflection\DocBlock;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Finder\Finder;
use Zend\Code\Generator\DocBlockGenerator;

abstract class AbstractAnnotationCommand extends Command
{
    const PARENT_CLASS = null;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     * @return void
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
    }

    protected function getArguments(): array
    {
        return [
            ['class', InputArgument::OPTIONAL, 'The class name to generate annotations for'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['folder', null, InputOption::VALUE_OPTIONAL, 'The folder to scan for classes to annotate'],
        ];
    }

    /**
     * Handle the command call.
     *
     * @return int
     * @throws ReflectionException
     */
    public function handle()
    {
        if ($this->argument('class')) {
            $this->annotateClass($this->argument('class'));
            return 0;
        }

        $this->annotateFolder();
    }

    /**
     * Annotate classes in a given folder
     *
     * @return void
     * @throws ReflectionException
     */
    protected function annotateFolder()
    {
        $classes = new ClassIterator($this->getClassFinder());

        $classes->enableAutoloading();

        /** @var ReflectionClass $reflection */
        foreach ($classes as $reflection) {
            if ($reflection->isSubclassOf(static::PARENT_CLASS)) {
                $this->annotate($reflection);
            }
        }
    }

    /**
     * Annotate a specific class by name
     *
     * @param string $className
     * @return void
     * @throws ReflectionException
     */
    protected function annotateClass(string $className)
    {
        if (!is_subclass_of($className, Enum::class)) {
            throw new InvalidArgumentException(
                sprintf('The given class must be an instance of %s: %s', static::PARENT_CLASS, $className)
            );
        }

        $reflection = new ReflectionClass($className);
        $this->annotate($reflection);
    }

    /**
     * Write new DocBlock to the class
     *
     * @param ReflectionClass $reflectionClass
     * @param DocBlock        $docBlock
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function updateClassDocblock(ReflectionClass $reflectionClass, DocBlockGenerator $docBlock)
    {
        $shortName = $reflectionClass->getShortName();
        $fileName = $reflectionClass->getFileName();
        $contents = $this->filesystem->get($fileName);

        $classDeclaration = "class {$shortName}";

        if ($reflectionClass->isFinal()) {
            $classDeclaration = "final {$classDeclaration}";
        } elseif ($reflectionClass->isAbstract()) {
            $classDeclaration = "abstract {$classDeclaration}";
        }

        // Remove existing docblock
        preg_replace(
            sprintf('#(\/\*(?:[^*]|\n|(?:\*(?:[^\/]|\n)))*\*\/)?[\n]%s#ms', preg_quote($classDeclaration)),
            $classDeclaration,
            $contents
        );

        $classDeclarationOffset = strpos($contents, $classDeclaration);
        // Make sure we don't replace too much
        $contents = substr_replace(
            $contents,
            $docBlock->generate(). $classDeclaration,
            $classDeclarationOffset,
            strlen($classDeclaration)
        );

        $this->filesystem->put($fileName, $contents);
        $this->info("Wrote new phpDocBlock to {$fileName}.");
    }

    /**
     * Merge new docblock tags into the existing docblock, if it exists.
     *
     * @param array         $tags
     * @param DocBlock|null $existingDocblock
     * @return DocBlock
     */
    protected function mergeTagsIntoDocblock(array $tags, ?DocBlock $existingDocblock = null): DocBlock
    {
        if ($existingDocblock === null) {
            return new DocBlock('', null, $tags);
        }

        return new DocBlock(
            $existingDocblock->getSummary(),
            $existingDocblock->getDescription(),
            array_merge($existingDocblock->getTags(), $tags)
        );
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @return void
     *
     * @throws ReflectionException
     */
    abstract protected function annotate(ReflectionClass $reflectionClass);

    abstract protected function getClassFinder(): Finder;
}
