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

    /** @return iterable<array{string}> */
    public static function provideData(): iterable
    {
        foreach (self::yieldFilesFromDirectory(__DIR__ . '/Usages') as $fileArray) {
            [$file] = $fileArray;

            // See https://wiki.php.net/rfc/dynamic_class_constant_fetch
            if (version_compare(PHP_VERSION, '8.3.0', '<')) {
                if ($file === __DIR__ . '/Usages/fromKey.dynamic_class_constant_fetch.php.inc') {
                    continue;
                }
            } else {
                if ($file === __DIR__ . '/Usages/fromKey.php.inc') {
                    continue;
                }
            }

            yield $fileArray;
        }
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/usages.php';
    }
}
