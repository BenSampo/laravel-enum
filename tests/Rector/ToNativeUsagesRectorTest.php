<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Rector;

use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

/** @see \BenSampo\Enum\Rector\ToNativeUsagesRector */
final class ToNativeUsagesRectorTest extends AbstractRectorTestCase
{
    /** @dataProvider provideData */
    #[DataProvider('provideData')]
    public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    /** @return iterable<string> */
    public static function provideData(): iterable
    {
        return self::yieldFilesFromDirectory(__DIR__ . '/Usages');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/usages.php';
    }
}
