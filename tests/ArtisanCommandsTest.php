<?php

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Tests\Enums\Annotate\AnnotateTestOneEnum;
use BenSampo\Enum\Tests\Enums\MixedKeyFormatsAnnotated;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Filesystem\Filesystem;

class ArtisanCommandsTest extends ApplicationTestCase
{
    public function test_artisan_commands_are_registered()
    {
        $commands = $this->app[Kernel::class]->all();

        $this->assertArrayHasKey('enum:annotate', $commands);
        $this->assertArrayHasKey('make:enum', $commands);
    }

    public function test_annotate_single_enum_class()
    {
        /** @var Filesystem $fileSystem */
        $fileSystem = $this->app[Filesystem::class];

        $fileSystem->copyDirectory(__DIR__ . '/Enums/AnnotateOriginals', __DIR__ . '/Enums/Annotate');

        $this->assertSame(
            0,
            $this->artisan('enum:annotate', ['class' => AnnotateTestOneEnum::class])
        );

        $newClass = $fileSystem->get(__DIR__ . '/Enums/Annotate/AnnotateTestOneEnum.php');

        $this->assertStringEqualsFile(__DIR__ . '/fixtures/annotated_class_one', $newClass);
    }

    public function test_annotate_single_enum_class_one()
    {
        /** @var Filesystem $fileSystem */
        $fileSystem = $this->app[Filesystem::class];

        $fileSystem->copyDirectory(__DIR__ . '/Enums/AnnotateOriginals', __DIR__ . '/Enums/Annotate');

        $this->assertSame(
            0,
            $this->artisan('enum:annotate', ['class' => AnnotateTestOneEnum::class])
        );

        $newClass = $fileSystem->get(__DIR__ . '/Enums/Annotate/AnnotateTestOneEnum.php');

        $this->assertStringEqualsFile(__DIR__ . '/fixtures/annotated_class_one', $newClass);
    }

    public function test_annotate_folder_enums()
    {
        /** @var Filesystem $fileSystem */
        $fileSystem = $this->app[Filesystem::class];

        $fileSystem->copyDirectory(__DIR__ . '/Enums/AnnotateOriginals', __DIR__ . '/Enums/Annotate');

        $this->assertSame(
            0,
            $this->artisan('enum:annotate', ['--folder' => __DIR__ . '/Enums/Annotate'])
        );

        $newClassOne = $fileSystem->get(__DIR__ . '/Enums/Annotate/AnnotateTestOneEnum.php');
        $newClassTwo = $fileSystem->get(__DIR__ . '/Enums/Annotate/AnnotateTestTwoEnum.php');

        $this->assertStringEqualsFile(__DIR__ . '/fixtures/annotated_class_one', $newClassOne);
        $this->assertStringEqualsFile(__DIR__ . '/fixtures/annotated_class_two', $newClassTwo);
    }

    public function test_annotate_enum_with_existing_docblock_is_not_changed()
    {
        /** @var Filesystem $fileSystem */
        $fileSystem = $this->app[Filesystem::class];

        $original = $fileSystem->get(__DIR__ . '/Enums/MixedKeyFormatsAnnotated.php');


        $this->assertSame(
            0,
            $this->artisan('enum:annotate', ['class' => MixedKeyFormatsAnnotated::class])
        );

        $newClass = $fileSystem->get(__DIR__ . '/Enums/MixedKeyFormatsAnnotated.php');
        $this->assertSame($original, $newClass);
    }
}
