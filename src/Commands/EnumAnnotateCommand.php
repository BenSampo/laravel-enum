<?php declare(strict_types=1);

namespace BenSampo\Enum\Commands;

use BenSampo\Enum\Enum;
use Composer\ClassMapGenerator\ClassMapGenerator;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Laminas\Code\Generator\DocBlock\Tag\MethodTag;
use Laminas\Code\Generator\DocBlock\Tag\TagInterface;
use Laminas\Code\Generator\DocBlockGenerator;
use Laminas\Code\Reflection\DocBlockReflection;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class EnumAnnotateCommand extends Command
{
    protected $name = 'enum:annotate';

    protected $description = 'Generate DocBlock annotations for enum classes';

    protected Filesystem $filesystem;

    /** @return array<int, array<int, mixed>> */
    protected function getArguments(): array
    {
        return [
            ['class', InputArgument::OPTIONAL, 'The class name to generate annotations for'],
        ];
    }

    /** @return array<int, array<int, mixed>> */
    protected function getOptions(): array
    {
        return [
            ['folder', null, InputOption::VALUE_OPTIONAL, 'The folder to scan for classes to annotate'],
        ];
    }

    public function handle(Filesystem $filesystem): int
    {
        $this->filesystem = $filesystem;

        $class = $this->argument('class');
        if ($class) {
            $this->annotateClass($class);

            return 0;
        }

        $this->annotateFolder();

        return 0;
    }

    protected function annotateClass(string $className): void
    {
        if (! is_subclass_of($className, Enum::class)) {
            $parentClass = Enum::class;
            throw new \InvalidArgumentException("The given class {$className} must be an instance of {$parentClass}.");
        }

        $reflection = new \ReflectionClass($className);
        $this->annotate($reflection);
    }

    protected function annotateFolder(): void
    {
        foreach (ClassMapGenerator::createMap($this->searchDirectory()) as $class => $_) {
            $reflection = new \ReflectionClass($class);

            if ($reflection->isSubclassOf(Enum::class)) {
                $this->annotate($reflection);
            }
        }
    }

    /** @param  \ReflectionClass<\BenSampo\Enum\Enum<mixed>> $reflectionClass */
    protected function annotate(\ReflectionClass $reflectionClass): void
    {
        $docBlock = $this->getDocBlock($reflectionClass);
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
        $quotedClassDeclaration = preg_quote($classDeclaration);
        $contents = preg_replace(
            "#\\r?\\n?\/\*[\s\S]*?\*\/(\\r?\\n)?{$quotedClassDeclaration}#ms",
            "\$1{$classDeclaration}",
            $contents,
        );

        // Make sure we don't replace too much
        $contents = substr_replace(
            $contents,
            "{$docBlock->generate()}{$classDeclaration}",
            strpos($contents, $classDeclaration),
            strlen($classDeclaration),
        );

        $this->filesystem->put($fileName, $contents);
        $this->info("Wrote new phpDocBlock to {$fileName}.");
    }

    /** @param  \ReflectionClass<\BenSampo\Enum\Enum<mixed>> $reflectionClass */
    protected function getDocBlock(\ReflectionClass $reflectionClass): DocBlockGenerator
    {
        $docBlock = DocBlockGenerator::fromArray([])
            ->setWordWrap(false);

        $originalDocBlock = null;

        $docComment = $reflectionClass->getDocComment();
        if ($docComment) {
            $docBlockReflection = new DocBlockReflection(ltrim($docComment));
            $originalDocBlock = DocBlockGenerator::fromReflection($docBlockReflection);

            $docBlock->setLongDescription($this->getDocblockWithoutTags($docBlockReflection));
        }

        $docBlock->setTags($this->getDocblockTags(
            $originalDocBlock,
            $reflectionClass
        ));

        return $docBlock;
    }

    protected function getDocblockWithoutTags(DocBlockReflection $docBlockReflection): string
    {
        $docBlockContents = $docBlockReflection->getContents();
        // We can remove all tags here, as we add them back in with getDocblockTags
        $withoutTags = preg_replace('/@.*$/m', '', $docBlockContents);

        return trim($withoutTags);
    }

    /**
     * @param  \ReflectionClass<\BenSampo\Enum\Enum<mixed>> $reflectionClass
     *
     * @return array<\Laminas\Code\Generator\DocBlock\Tag\TagInterface>
     */
    protected function getDocblockTags(DocBlockGenerator|null $originalDocblock, \ReflectionClass $reflectionClass): array
    {
        $constants = $reflectionClass->getConstants();
        $constantKeys = array_keys($constants);

        $tags = array_map(
            static fn (mixed $value, string $constantName): MethodTag => new MethodTag($constantName, ['static'], null, true),
            $constants,
            $constantKeys,
        );

        if ($originalDocblock) {
            $tags = array_merge(
                $tags,
                array_filter($originalDocblock->getTags(), fn (TagInterface $tag): bool => ! $tag instanceof MethodTag
                    || ! in_array($tag->getMethodName(), $constantKeys, true))
            );
        }

        return $tags;
    }

    protected function searchDirectory(): string
    {
        return $this->option('folder')
            ?? app_path('Enums');
    }
}
