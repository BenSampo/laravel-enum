<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;

final class ArtisanCommandsTest extends ApplicationTestCase
{
    public function testArtisanCommandsAreRegistered(): void
    {
        $consoleKernel = $this->app->make(ConsoleKernel::class);
        assert($consoleKernel instanceof ConsoleKernel);
        $commands = $consoleKernel->all();

        $this->assertArrayHasKey('enum:annotate', $commands);
        $this->assertArrayHasKey('enum:to-native', $commands);
        $this->assertArrayHasKey('make:enum', $commands);
    }
}
