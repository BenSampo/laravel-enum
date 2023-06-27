<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use Illuminate\Filesystem\Filesystem;

final class EnumAnnotateCommandTest extends ApplicationTestCase
{
    /** @dataProvider classes */
    public function testAnnotateClass(string $class): void
    {
        $filesystem = $this->filesystem();
        $this->prepareAnnotateDirectory($filesystem, 'AnnotateOriginals');

        $this->artisan('enum:annotate', ['class' => "BenSampo\\Enum\\Tests\\Enums\\Annotate\\{$class}"])
            ->assertExitCode(0);

        $this->assertAnnotatedClassMatchesFixture($filesystem, $class);
    }

    /** @dataProvider classes */
    public function testAnnotateClassAlreadyAnnotated(string $class): void
    {
        $filesystem = $this->filesystem();
        $this->prepareAnnotateDirectory($filesystem, 'AnnotateFixtures');

        $this->artisan('enum:annotate', ['class' => "BenSampo\\Enum\\Tests\\Enums\\Annotate\\{$class}"])
            ->assertExitCode(0);

        $this->assertAnnotatedClassMatchesFixture($filesystem, $class);
    }

    /** @return iterable<array{string}> */
    public static function classes(): iterable
    {
        yield ['EnumWithMultipleLineComments'];
        yield ['EnumWithSingleLineComment'];
        yield ['LongConstantName'];
        yield ['ManyLongConstantNames'];
        yield ['MixedKeys'];
    }

    /** @return iterable<array{string}> */
    public static function sources(): iterable
    {
        yield ['AnnotateOriginals'];
        yield ['AnnotateFixtures'];
    }

    /** @dataProvider sources */
    public function testAnnotateFolder(string $source): void
    {
        $filesystem = $this->filesystem();
        $this->prepareAnnotateDirectory($filesystem, $source);

        $this->artisan('enum:annotate', ['--folder' => __DIR__ . '/Enums/Annotate'])->assertExitCode(0);

        foreach (self::classes() as $class) {
            $this->assertAnnotatedClassMatchesFixture($filesystem, $class[0]);
        }
    }

    private function filesystem(): Filesystem
    {
        $filesystem = $this->app->make(Filesystem::class);
        assert($filesystem instanceof Filesystem);

        return $filesystem;
    }

    private function assertAnnotatedClassMatchesFixture(Filesystem $filesystem, string $class): void
    {
        $this->assertStringEqualsFile(
            __DIR__ . "/Enums/AnnotateFixtures/{$class}.php",
            $filesystem->get(__DIR__ . "/Enums/Annotate/{$class}.php")
        );
    }

    private function prepareAnnotateDirectory(Filesystem $filesystem, string $source): void
    {
        $filesystem->copyDirectory(__DIR__ . "/Enums/{$source}", __DIR__ . '/Enums/Annotate');
    }
}
