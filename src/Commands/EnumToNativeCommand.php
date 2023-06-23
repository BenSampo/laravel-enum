<?php declare(strict_types=1);

namespace BenSampo\Enum\Commands;

use BenSampo\Enum\Enum;
use Laminas\Code\Generator\DocBlock\Tag\MethodTag;
use Laminas\Code\Generator\DocBlock\Tag\TagInterface;
use ReflectionClass;
use InvalidArgumentException;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Composer\ClassMapGenerator\ClassMapGenerator;
use Laminas\Code\Generator\DocBlockGenerator;
use Laminas\Code\Reflection\DocBlockReflection;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use function preg_quote;
use function preg_replace;
use function strlen;
use function strpos;
use function substr_replace;

class EnumToNativeCommand extends Command
{
    protected $name = 'enum:to-native';

    protected $description = 'Convert a class that extends BenSampo\Enum\Enum to a native PHP enum';

    protected Filesystem $filesystem;

    /**
     * @return array<int, array<int, mixed>>
     */
    protected function getArguments(): array
    {
        return [
            ['class', InputArgument::OPTIONAL, 'The class name to convert'],
        ];
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    protected function getOptions(): array
    {
        return [
            ['folder', null, InputOption::VALUE_OPTIONAL, 'The folder to scan for classes to convert'],
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

        $this->convertFolder();

        return 0;
    }

    protected function annotateClass(string $className): void
    {
        if (!is_subclass_of($className, Enum::class)) {
            $parentClass = Enum::class;
            throw new InvalidArgumentException("The given class {$className} must be an instance of {$parentClass}.");
        }

        $this->convert(new ReflectionClass($className));
    }

    protected function convertFolder(): void
    {
        foreach (ClassMapGenerator::createMap($this->searchDirectory()) as $class => $_) {
            $reflection = new ReflectionClass($class);

            if ($reflection->isSubclassOf(Enum::class)) {
                $this->convert($reflection);
            }
        }
    }

    /**
     * @param  \ReflectionClass<\BenSampo\Enum\Enum<mixed>> $reflectionClass
     */
    protected function convert(ReflectionClass $reflectionClass): void
    {
        $docBlock1 = $this->getDocBlock($reflectionClass);
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
            "{$docBlock1->generate()}{$classDeclaration}",
            strpos($contents, $classDeclaration),
            strlen($classDeclaration),
        );

        $this->filesystem->put($fileName, $contents);
        $this->info("Wrote new phpDocBlock to {$fileName}.");
    }

    /**
     * @param  \ReflectionClass<\BenSampo\Enum\Enum<mixed>> $reflectionClass
     */
    protected function getDocBlock(ReflectionClass $reflectionClass): DocBlockGenerator
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
     * @return array<\Laminas\Code\Generator\DocBlock\Tag\TagInterface>
     */
    protected function getDocblockTags(DocBlockGenerator|null $originalDocblock, ReflectionClass $reflectionClass): array
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
                array_filter($originalDocblock->getTags(), fn (TagInterface $tag): bool =>
                    ! $tag instanceof MethodTag
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
