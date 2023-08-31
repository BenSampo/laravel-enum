<?php declare(strict_types=1);

namespace BenSampo\Enum\Commands;

use BenSampo\Enum\Enum;
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
        if ($class) {
            $class = ltrim($class, '\\');
        }

        $env = [
            self::TO_NATIVE_CLASS_ENV => $class ?? Enum::class,
            self::BASE_RECTOR_CONFIG_PATH_ENV => base_path('rector.php'),
        ];
        $withPipedOutput = function (string $type, string $output): void {
            echo $output;
        };
        $run = fn (string $command) => Process::env($env)
            ->timeout(0) // Unlimited, rector can take quite a while
            ->run($command, $withPipedOutput);

        if ($class) {
            if (! class_exists($class)) {
                $this->error("Class does not exist: {$class}.");

                return 1;
            }

            // If a specific class is given, we can do both conversion steps at once
            // since the usages can still be recognized by the class name.
            $usagesAndImplementationConfig = realpath(__DIR__ . '/../Rector/usages-and-implementation.php');
            $convertUsagesAndImplementation = "vendor/bin/rector process --clear-cache --config={$usagesAndImplementationConfig}";
            $this->info("Converting {$class}, running: {$convertUsagesAndImplementation}");
            $run($convertUsagesAndImplementation);
        } else {
            // If not, we have to do two steps to avoid partial conversion,
            // since the usages conversion relies on the enums extending BenSampo\Enum\Enum.
            $usagesConfig = realpath(__DIR__ . '/../Rector/usages.php');
            $convertUsages = "vendor/bin/rector process --clear-cache --config={$usagesConfig}";
            $this->info("Converting usages, running: {$convertUsages}");
            $run($convertUsages);

            $implementationConfig = realpath(__DIR__ . '/../Rector/implementation.php');
            $convertImplementation = "vendor/bin/rector process --clear-cache --config={$implementationConfig}";
            $this->info("Converting implementation, running: {$convertImplementation}");
            $run($convertImplementation);
        }

        return 0;
    }
}
