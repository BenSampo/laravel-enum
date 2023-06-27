<?php declare(strict_types=1);

namespace BenSampo\Enum\Commands;

use BenSampo\Enum\Enum;
use Composer\ClassMapGenerator\ClassMapGenerator;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Laminas\Code\Generator\EnumGenerator\EnumGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class EnumToNativeCommand extends Command
{
    protected $name = 'enum:to-native';

    protected $description = 'Convert a class that extends BenSampo\Enum\Enum to a native PHP enum';

    protected Filesystem $filesystem;

    /** @return array<int, array<int, mixed>> */
    protected function getArguments(): array
    {
        return [
            ['class', InputArgument::OPTIONAL, 'The class name to convert'],
        ];
    }

    /** @return array<int, array<int, mixed>> */
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
        if (! is_subclass_of($className, Enum::class)) {
            $parentClass = Enum::class;
            throw new \InvalidArgumentException("The given class {$className} must be an instance of {$parentClass}.");
        }

        $this->convert(new \ReflectionClass($className));
    }

    protected function convertFolder(): void
    {
        foreach (ClassMapGenerator::createMap($this->searchDirectory()) as $class => $_) {
            $reflection = new \ReflectionClass($class);

            if ($reflection->isSubclassOf(Enum::class)) {
                $this->convert($reflection);
            }
        }
    }

    /** @param  \ReflectionClass<\BenSampo\Enum\Enum<mixed>> $reflectionClass */
    protected function convert(\ReflectionClass $reflectionClass): void
    {
        $type = null;
        $constants = $reflectionClass->getConstants();
        $className = $reflectionClass->name;
        foreach ($constants as $name => $value) {
            $valueType = gettype($value);
            if ($valueType === 'integer') {
                $valueType = 'int';
            }

            if ($type === null) {
                $type = $valueType;
                continue;
            }

            if ($type !== $valueType) {
                throw new \Exception("Cannot convert class {$className} with mixed constant value types to native enum.");
            }
        }

        if ($type === null) {
            throw new \Exception("Cannot convert class {$className} with no constants to native enum.");
        }

        if ($type !== 'int' && $type !== 'string') {
            throw new \Exception("Cannot convert class {$className} with constant values of type {$type} to native enum, only 'int' or 'string' are allowed.");
        }

        $fileName = $reflectionClass->getFileName();

        // @phpstan-ignore-next-line fails when missing laminas/laminas-code 4 and on lower PHPStan versions
        $enum = EnumGenerator::withConfig([
            'name' => $className,
            'backedCases' => [
                'type' => $type,
                'cases' => $constants,
            ],
        ])->generate();
        $contents = <<<PHP
        <?php declare(strict_types=1);

        {$enum}
        PHP;

        $this->filesystem->put($fileName, $contents);
        $this->info("Converted {$className} to native enum.");
    }

    protected function searchDirectory(): string
    {
        return $this->option('folder')
            ?? app_path('Enums');
    }
}
