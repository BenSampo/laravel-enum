<?php
declare(strict_types=1);

namespace BenSampo\Enum\Commands;

use ReflectionClass;
use InvalidArgumentException;
use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;
use Illuminate\Filesystem\Filesystem;
use Laminas\Code\Generator\DocBlockGenerator;
use Laminas\Code\Reflection\DocBlockReflection;
use hanneskod\classtools\Iterator\ClassIterator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

abstract class AbstractAnnotationCommand extends Command
{
    public const PARENT_CLASS = null;

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
     */
    public function handle()
    {
        if ($this->argument('class')) {
            $this->annotateClass($this->argument('class'));

            return 0;
        }

        $this->annotateFolder();

        return 0;
    }

    /**
     * Annotate classes in a given folder
     *
     * @return void
     */
    protected function annotateFolder()
    {
        $classes = new ClassIterator($this->getClassFinder());

        $classes->enableAutoloading();

        /** @var \ReflectionClass $reflection */
        foreach ($classes as $reflection) {
            if ($reflection->isSubclassOf(static::PARENT_CLASS)) {
                $this->annotate($reflection);
            }
        }
    }

    /**
     * Annotate a specific class by name
     *
     * @param  string  $className
     * @return void
     *
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    protected function annotateClass(string $className)
    {
        if (!is_subclass_of($className, static::PARENT_CLASS)) {
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
     * @param  \ReflectionClass  $reflectionClass
     * @param  \Laminas\Code\Generator\DocBlockGenerator  $docBlock
     * @return void
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
        $contents = preg_replace(
            sprintf('#([\n]?\/\*(?:[^*]|\n|(?:\*(?:[^\/]|\n)))*\*\/)?[\n]?%s#ms', preg_quote($classDeclaration)),
            "\n" . $classDeclaration,
            $contents
        );

        $classDeclarationOffset = strpos($contents, $classDeclaration);
        // Make sure we don't replace too much
        $contents = substr_replace(
            $contents,
            sprintf("%s%s", $docBlock->generate(), $classDeclaration),
            $classDeclarationOffset,
            strlen($classDeclaration)
        );

        $this->filesystem->put($fileName, $contents);
        $this->info("Wrote new phpDocBlock to {$fileName}.");
    }

    protected function getDocBlock(ReflectionClass $reflectionClass): DocBlockGenerator
    {
        $docBlock = DocBlockGenerator::fromArray([]);

        $originalDocBlock = null;

        if ($reflectionClass->getDocComment()) {
            $originalDocBlock = DocBlockGenerator::fromReflection(
                new DocBlockReflection(ltrim($reflectionClass->getDocComment()))
            );

            if ($originalDocBlock->getShortDescription()) {
                $docBlock->setShortDescription($originalDocBlock->getShortDescription());
            }

            if ($originalDocBlock->getLongDescription()) {
                $docBlock->setLongDescription($originalDocBlock->getLongDescription());
            }
        }

        $docBlock->setTags($this->getDocblockTags(
            $originalDocBlock ? $originalDocBlock->getTags() : [],
            $reflectionClass
        ));

        return $docBlock;
    }

    abstract protected function getDocblockTags(array $originalTags, ReflectionClass $reflectionClass): array;

    abstract protected function annotate(ReflectionClass $reflectionClass);

    abstract protected function getClassFinder(): Finder;
}
