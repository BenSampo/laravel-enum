<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Commands\EnumToNativeCommand;
use BenSampo\Enum\Enum;
use BenSampo\Enum\Tests\Enums\UserType;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\Process;

final class EnumToNativeCommandTest extends ApplicationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! class_exists(Process::class)) {
            $this->markTestSkipped('Requires Laravel 10.');
        }
    }

    public function testAll(): void
    {
        $process = Process::fake();

        $this->artisan('enum:to-native')
            ->assertExitCode(0);

        $count = 0;
        $process->assertRan(function (PendingProcess $process) use (&$count): bool {
            ++$count;
            $this->assertSame([
                EnumToNativeCommand::TO_NATIVE_CLASS_ENV => Enum::class,
                EnumToNativeCommand::BASE_RECTOR_CONFIG_PATH_ENV => base_path('rector.php'),
            ], $process->environment);
            $this->assertMatchesRegularExpression(
                match ($count) {
                    1 => '#^vendor/bin/rector process --clear-cache --config=/.+/src/Rector/usages\.php$#',
                    2 => '#^vendor/bin/rector process --clear-cache --config=/.+/src/Rector/implementation\.php$#',
                    default => throw new \Exception('Only expected 2 processes'),
                },
                $process->command
            );

            return true;
        });
    }

    public function testClass(): void
    {
        $process = Process::fake();

        $this->artisan('enum:to-native', ['class' => UserType::class])
            ->assertExitCode(0);

        $process->assertRan(function (PendingProcess $process): bool {
            $this->assertSame([
                EnumToNativeCommand::TO_NATIVE_CLASS_ENV => UserType::class,
                EnumToNativeCommand::BASE_RECTOR_CONFIG_PATH_ENV => base_path('rector.php'),
            ], $process->environment);
            $this->assertMatchesRegularExpression(
                '#^vendor/bin/rector process --clear-cache --config=/.+/src/Rector/usages-and-implementation\.php$#',
                $process->command
            );

            return true;
        });
    }

    public function testClassDoesNotExist(): void
    {
        $process = Process::fake();

        $this->artisan('enum:to-native', ['class' => 'does-not-exist'])
            ->assertExitCode(1);

        $process->assertNothingRan();
    }
}
