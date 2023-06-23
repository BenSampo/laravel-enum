<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use Illuminate\Filesystem\Filesystem;

final class EnumToNativeCommandTest extends ApplicationTestCase
{
    /** @dataProvider classes */
    public function test_class_to_native(string $class): void
    {
        $filesystem = $this->filesystem();
        $this->prepareToNativeDirectory($filesystem, 'ToNativeOriginals');

        $this->artisan('enum:to-native', ['class' => "BenSampo\\Enum\\Tests\\Enums\\ToNative\\{$class}"])
            ->assertExitCode(0);

        $this->assertNativeEnumMatchesFixture($filesystem, $class);
    }

    /** @dataProvider classes */
    public function test_annotate_class_already_annotated(string $class): void
    {
        $filesystem = $this->filesystem();
        $this->prepareToNativeDirectory($filesystem, 'AnnotateFixtures');

        $this->artisan('enum:annotate', ['class' => "BenSampo\\Enum\\Tests\\Enums\\Annotate\\{$class}"])
            ->assertExitCode(0);

        $this->assertNativeEnumMatchesFixture($filesystem, $class);
    }

    /** @return iterable<array{string}> */
    public static function classes(): iterable
    {
        yield ['UserType'];
    }

    /** @return iterable<array{string}> */
    public static function sources(): iterable
    {
        yield ['ToNativeOriginals'];
        yield ['ToNativeFixtures'];
    }

    /** @dataProvider sources */
    public function test_annotate_folder(string $source): void
    {
        $filesystem = $this->filesystem();
        $this->prepareToNativeDirectory($filesystem, $source);

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

    private function prepareToNativeDirectory(Filesystem $filesystem, string $source): void
    {
        $filesystem->copyDirectory(__DIR__ . "/Enums/{$source}", __DIR__ . '/Enums/ToNative');
    }
}
