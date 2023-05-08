<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Tests\Enums\EnumWithMultipleLineComments;
use BenSampo\Enum\Tests\Enums\EnumWithMultipleLineCommentsWithoutBlankLines;
use BenSampo\Enum\Tests\Enums\EnumWithSingleLineComment;
use BenSampo\Enum\Tests\Enums\EnumWithSingleLineCommentWithoutBlankLine;
use BenSampo\Enum\Tests\Enums\ManyLongConstantNames;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use BenSampo\Enum\Tests\Enums\LongConstantName;
use BenSampo\Enum\Tests\Enums\MixedKeyFormatsAnnotated;
use BenSampo\Enum\Tests\Enums\Annotate\AnnotateTestOneEnum;

final class ArtisanCommandsTest extends ApplicationTestCase
{
    public function test_artisan_commands_are_registered(): void
    {
        $consoleKernel = $this->app->make(ConsoleKernel::class);
        assert($consoleKernel instanceof ConsoleKernel);
        $commands = $consoleKernel->all();

        $this->assertArrayHasKey('enum:annotate', $commands);
        $this->assertArrayHasKey('make:enum', $commands);
    }

    public function test_annotate_single_enum_class(): void
    {
        $filesystem = $this->app->make(Filesystem::class);
        assert($filesystem instanceof Filesystem);

        $filesystem->copyDirectory(__DIR__ . '/Enums/AnnotateOriginals', __DIR__ . '/Enums/Annotate');
        $this->artisan('enum:annotate', ['class' => AnnotateTestOneEnum::class])->assertExitCode(0);

        $newClass = $filesystem->get(__DIR__ . '/Enums/Annotate/AnnotateTestOneEnum.php');
        $this->assertStringEqualsFile(__DIR__ . '/fixtures/annotated_class_one', $newClass);
    }

    public function test_annotate_single_enum_class_one(): void
    {
        $filesystem = $this->app->make(Filesystem::class);
        assert($filesystem instanceof Filesystem);

        $filesystem->copyDirectory(__DIR__ . '/Enums/AnnotateOriginals', __DIR__ . '/Enums/Annotate');
        $this->artisan('enum:annotate', ['class' => AnnotateTestOneEnum::class])->assertExitCode(0);

        $newClass = $filesystem->get(__DIR__ . '/Enums/Annotate/AnnotateTestOneEnum.php');
        $this->assertStringEqualsFile(__DIR__ . '/fixtures/annotated_class_one', $newClass);
    }

    public function test_annotate_folder_enums(): void
    {
        $filesystem = $this->app->make(Filesystem::class);
        assert($filesystem instanceof Filesystem);

        $filesystem->copyDirectory(__DIR__ . '/Enums/AnnotateOriginals', __DIR__ . '/Enums/Annotate');
        $this->artisan('enum:annotate', ['--folder' => __DIR__ . '/Enums/Annotate'])->assertExitCode(0);

        $newClassOne = $filesystem->get(__DIR__ . '/Enums/Annotate/AnnotateTestOneEnum.php');
        $newClassTwo = $filesystem->get(__DIR__ . '/Enums/Annotate/AnnotateTestTwoEnum.php');

        $this->assertStringEqualsFile(__DIR__ . '/fixtures/annotated_class_one', $newClassOne);
        $this->assertStringEqualsFile(__DIR__ . '/fixtures/annotated_class_two', $newClassTwo);
    }

    public function test_annotate_enum_with_existing_docblock_is_not_changed(): void
    {
        $filesystem = $this->app->make(Filesystem::class);
        assert($filesystem instanceof Filesystem);

        $original = $filesystem->get(__DIR__ . '/Enums/MixedKeyFormatsAnnotated.php');
        $this->artisan('enum:annotate', ['class' => MixedKeyFormatsAnnotated::class])->assertExitCode(0);

        $newClass = $filesystem->get(__DIR__ . '/Enums/MixedKeyFormatsAnnotated.php');
        $this->assertSame($original, $newClass);
    }

    public function test_annotate_enum_does_not_wrap_long_constant_names_in_docblock(): void
    {
        $filesystem = $this->app->make(Filesystem::class);
        assert($filesystem instanceof Filesystem);

        $original = $filesystem->get(__DIR__ . '/Enums/LongConstantName.php');
        $this->artisan('enum:annotate', ['class' => LongConstantName::class])->assertExitCode(0);

        $newClass = $filesystem->get(__DIR__ . '/Enums/LongConstantNameAnnotated.php');
        $this->assertSame($original, $newClass);
    }

    public function test_annotate_enum_does_not_purge_many_constants_with_long_names(): void
    {
        $filesystem = $this->app->make(Filesystem::class);
        assert($filesystem instanceof Filesystem);

        $original = $filesystem->get(__DIR__ . '/Enums/ManyLongConstantNames.php');
        $this->artisan('enum:annotate', ['class' => ManyLongConstantNames::class])->assertExitCode(0);

        $newClass = $filesystem->get(__DIR__ . '/Enums/ManyLongConstantNames.php');
        $this->assertSame($original, $newClass);
    }

    public function test_annotate_enum_with_multiple_line_comments_with_blank_lines(): void
    {
        $filesystem = $this->app->make(Filesystem::class);
        assert($filesystem instanceof Filesystem);

        $original = $filesystem->get(__DIR__ . '/Enums/EnumWithMultipleLineComments.php');
        $this->artisan('enum:annotate', ['class' => EnumWithMultipleLineComments::class])->assertExitCode(0);

        $newClass = $filesystem->get(__DIR__ . '/Enums/EnumWithMultipleLineComments.php');
        $this->assertSame($original, $newClass);
    }

    public function test_annotate_enum_with_single_line_comment_with_blank_line(): void
    {
        $filesystem = $this->app->make(Filesystem::class);
        assert($filesystem instanceof Filesystem);

        $original = $filesystem->get(__DIR__ . '/Enums/EnumWithSingleLineComment.php');
        $this->artisan('enum:annotate', ['class' => EnumWithSingleLineComment::class])->assertExitCode(0);

        $newClass = $filesystem->get(__DIR__ . '/Enums/EnumWithSingleLineComment.php');
        $this->assertSame($original, $newClass);
    }
}
