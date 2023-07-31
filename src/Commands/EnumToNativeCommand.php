<?php declare(strict_types=1);

namespace BenSampo\Enum\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Console\Input\InputArgument;

class EnumToNativeCommand extends Command
{
    public const TO_NATIVE_CLASS_ENV = 'TO_NATIVE_CLASS';
    public const BASE_RECTOR_CONFIG_PATH_ENV = 'BASE_RECTOR_CONFIG_PATH';

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

        $env = [
            self::TO_NATIVE_CLASS_ENV => $class,
            self::BASE_RECTOR_CONFIG_PATH_ENV => base_path('rector.php'),
        ];
        $withPipedOutput = function (string $type, string $output): void {
            echo $output;
        };
        $run = fn (string $command) => Process::env($env)
            ->run($command, $withPipedOutput);

        $usagesConfig = realpath(__DIR__ . '/../Rector/usages.php');

        $convertUsages = "vendor/bin/rector process --clear-cache --config={$usagesConfig}";
        $this->info("Converting usages, running: {$convertUsages}");
        $run($convertUsages);

        $implementationConfig = realpath(__DIR__ . '/../Rector/implementation.php');
        $classFileName = $class
            ? (new \ReflectionClass($class))->getFileName()
            : null;

        $convertImplementation = "vendor/bin/rector process --clear-cache --config={$implementationConfig} {$classFileName}";
        $this->info("Converting implementation, running: {$convertImplementation}");
        $run($convertImplementation);

        return 0;
    }
}
