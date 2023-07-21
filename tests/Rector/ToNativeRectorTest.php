<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests\Rector;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class ToNativeRectorTest extends AbstractRectorTestCase
{
    /** @dataProvider provideData */
    public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    /** @return iterable<string> */
    public static function provideData(): iterable
    {
        return self::yieldFilesFromDirectory(__DIR__ . '/fixtures');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config.php';
    }
}
