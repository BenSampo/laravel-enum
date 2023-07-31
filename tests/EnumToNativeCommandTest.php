<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Commands\EnumToNativeCommand;
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
            $this->assertSame([], $process->environment);
            $this->assertMatchesRegularExpression(
                match ($count) {
                    1 => '#^vendor/bin/rector process --clear-cache --config=/.+/src/Rector/usages\.php$#',
                    2 => '#^vendor/bin/rector process --clear-cache --config=/.+/src/Rector/implementation\.php $#',
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

        $count = 0;
        $process->assertRan(function (PendingProcess $process) use (&$count): bool {
            ++$count;
            $this->assertSame([EnumToNativeCommand::CLASS_ENV => UserType::class], $process->environment);
            $this->assertMatchesRegularExpression(
                match ($count) {
                    1 => '#^vendor/bin/rector process --clear-cache --config=/.+/src/Rector/usages\.php$#',
                    2 => '#^vendor/bin/rector process --clear-cache --config=/.+/src/Rector/implementation\.php /.+/tests/Enums/UserType.php$#',
                    default => throw new \Exception('Only expected 2 processes'),
                },
                $process->command
            );

            return true;
        });
    }
}
