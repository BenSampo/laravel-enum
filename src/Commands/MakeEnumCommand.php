<?php declare(strict_types=1);

namespace BenSampo\Enum\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeEnumCommand extends GeneratorCommand
{
    protected $name = 'make:enum';

    protected $description = 'Create a new enum class';

    protected $type = 'Enum';

    protected function getStub(): string
    {
        return $this->option('flagged')
            ? $this->resolveStubPath('/stubs/enum.flagged.stub')
            : $this->resolveStubPath('/stubs/enum.stub');
    }

    protected function resolveStubPath(string $stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . $stub;
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return "{$rootNamespace}\Enums";
    }

    /** @return array<int, array<int, mixed>> */
    protected function getOptions(): array
    {
        return [
            ['flagged', 'f', InputOption::VALUE_NONE, 'Generate a flagged enum'],
        ];
    }
}
