<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use Illuminate\Filesystem\Filesystem;
use Laminas\Code\Generator\EnumGenerator\EnumGenerator;

final class EnumToNativeCommandTest extends ApplicationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! class_exists(EnumGenerator::class)) {
            $this->markTestSkipped('Missing laminas/laminas-code 4.');
        }
    }

    /** @dataProvider classes */
    public function test_class_to_native(string $class): void
    {
        $filesystem = $this->filesystem();
        $this->prepareToNativeDirectory($filesystem);

        $this->artisan('enum:to-native', ['class' => "BenSampo\\Enum\\Tests\\Enums\\ToNative\\{$class}"])
            ->assertExitCode(0);

        $this->assertNativeEnumMatchesFixture($filesystem, $class);
    }

    /** @return iterable<array{string}> */
    public static function classes(): iterable
    {
        yield ['UserType'];
    }

    public function test_annotate_folder(): void
    {
        $filesystem = $this->filesystem();
        $this->prepareToNativeDirectory($filesystem);

        $this->artisan('enum:to-native', ['--folder' => __DIR__ . '/Enums/ToNative'])->assertExitCode(0);

        foreach (self::classes() as $class) {
            $this->assertNativeEnumMatchesFixture($filesystem, $class[0]);
        }
    }

    private function filesystem(): Filesystem
    {
        $filesystem = $this->app->make(Filesystem::class);
        assert($filesystem instanceof Filesystem);

        return $filesystem;
    }

    private function assertNativeEnumMatchesFixture(Filesystem $filesystem, string $class): void
    {
        $this->assertStringEqualsFile(
            __DIR__ . "/Enums/ToNativeFixtures/{$class}.php",
            $filesystem->get(__DIR__ . "/Enums/ToNative/{$class}.php")
        );
    }

    private function prepareToNativeDirectory(Filesystem $filesystem): void
    {
        $filesystem->copyDirectory(__DIR__ . "/Enums/ToNativeOriginals", __DIR__ . '/Enums/ToNative');
    }
}
