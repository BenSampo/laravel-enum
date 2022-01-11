<?php

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Tests\Enums\Described;
use PHPUnit\Framework\TestCase;

final class EnumDescriptionTest extends TestCase
{
    public function test_defaults_to_readable_const(): void
    {
        $this->assertSame('No description', (new Described(Described::NO_DESCRIPTION))->description);
    }

    public function test_phpdoc_single_line(): void
    {
        $this->assertSame('just one line', (new Described(Described::SINGLE_LINE))->description);
    }

    public function test_phpdoc_multi_line(): void
    {
        $this->assertSame(<<<'PHPDOC'
more
than
one
line
PHPDOC
, (new Described(Described::MULTI_LINE))->description);
    }

    public function test_phpdoc_deprecated(): void
    {
        $this->assertSame('@deprecated because some reason', (new Described(Described::DEPRECATED))->description);
    }
}
