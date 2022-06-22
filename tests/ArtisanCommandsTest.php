<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Console\Kernel;
use BenSampo\Enum\Tests\Enums\LongConstantName;
use BenSampo\Enum\Tests\Enums\MixedKeyFormatsAnnotated;
use BenSampo\Enum\Tests\Enums\Annotate\AnnotateTestOneEnum;

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

        $this->artisan('enum:annotate', ['class' => AnnotateTestOneEnum::class])->assertExitCode(0);

        $newClass = $fileSystem->get(__DIR__ . '/Enums/Annotate/AnnotateTestOneEnum.php');

        $this->assertStringEqualsFile(__DIR__ . '/fixtures/annotated_class_one', $newClass);
    }

    public function test_annotate_single_enum_class_one()
    {
        /** @var Filesystem $fileSystem */
        $fileSystem = $this->app[Filesystem::class];

        $fileSystem->copyDirectory(__DIR__ . '/Enums/AnnotateOriginals', __DIR__ . '/Enums/Annotate');

        $this->artisan('enum:annotate', ['class' => AnnotateTestOneEnum::class])->assertExitCode(0);

        $newClass = $fileSystem->get(__DIR__ . '/Enums/Annotate/AnnotateTestOneEnum.php');

        $this->assertStringEqualsFile(__DIR__ . '/fixtures/annotated_class_one', $newClass);
    }

    public function test_annotate_folder_enums()
    {
        /** @var Filesystem $fileSystem */
        $fileSystem = $this->app[Filesystem::class];

        $fileSystem->copyDirectory(__DIR__ . '/Enums/AnnotateOriginals', __DIR__ . '/Enums/Annotate');

        $this->artisan('enum:annotate', ['--folder' => __DIR__ . '/Enums/Annotate'])->assertExitCode(0);

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

        $this->artisan('enum:annotate', ['class' => MixedKeyFormatsAnnotated::class])->assertExitCode(0);

        $newClass = $fileSystem->get(__DIR__ . '/Enums/MixedKeyFormatsAnnotated.php');
        $this->assertSame($original, $newClass);
    }

    public function test_annotate_enum_does_not_wrap_long_constant_names_in_docblock()
    {
        /** @var Filesystem $fileSystem */
        $fileSystem = $this->app[Filesystem::class];

        $original = $fileSystem->get(__DIR__ . '/Enums/LongConstantName.php');

        $this->artisan('enum:annotate', ['class' => LongConstantName::class])->assertExitCode(0);

        $newClass = $fileSystem->get(__DIR__ . '/Enums/LongConstantNameAnnotated.php');
        $this->assertSame($original, $newClass);
    }
}
