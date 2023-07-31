<?php declare(strict_types=1);

namespace BenSampo\Enum\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Console\Input\InputArgument;

class EnumToNativeCommand extends Command
{
    public const CLASS_ENV = 'TO_NATIVE_CLASS';

    protected $name = 'enum:to-native';

    protected $description = 'Use Rector to convert classes that extend BenSampo\Enum\Enum to native PHP enums';

    /** @return array<int, array<int, mixed>> */
    protected function getArguments(): array
    {
        return [
            ['class', InputArgument::OPTIONAL, 'The class name to convert'],
        ];
    }

    public function handle(): int
    {
        $class = $this->argument('class');

        $withPipedOutput = function (string $type, string $output): void {
            echo $output;
        };

        $usagesConfig = realpath(__DIR__ . '/../Rector/usages.php');
        Process::env($class ? [self::CLASS_ENV => $class] : [])
            ->run("vendor/bin/rector process --clear-cache --config={$usagesConfig}", $withPipedOutput);

        $implementationConfig = realpath(__DIR__ . '/../Rector/implementation.php');
        $classFileName = $class
            ? (new \ReflectionClass($class))->getFileName()
            : null;
        Process::env($class ? [self::CLASS_ENV => $class] : [])
            ->run("vendor/bin/rector process --clear-cache --config={$implementationConfig} {$classFileName}", $withPipedOutput);

        return 0;
    }
}
