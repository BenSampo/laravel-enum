<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Tests\Enums\SingleValue;
use PHPUnit\Framework\TestCase;

final class EnumRandomTest extends TestCase
{
    public function testCanGetRandomKey(): void
    {
        $key = SingleValue::getRandomKey();

        $this->assertSame(
            SingleValue::getKey(SingleValue::KEY),
            $key
        );
    }

    public function testCanGetRandomValue(): void
    {
        $value = SingleValue::getRandomValue();

        $this->assertSame(SingleValue::KEY, $value);
    }

    public function testCanGetRandomInstance(): void
    {
        $instance = SingleValue::getRandomInstance();

        $this->assertTrue(
            $instance->is(SingleValue::KEY)
        );
    }
}
