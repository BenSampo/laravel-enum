<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Tests\Enums\SingleValue;
use PHPUnit\Framework\TestCase;

class EnumRandomTest extends TestCase
{
    public function test_can_get_random_key()
    {
        $key = SingleValue::getRandomKey();

        $this->assertSame(
            SingleValue::getKey(SingleValue::KEY),
            $key
        );
    }

    public function test_can_get_random_value()
    {
        $value = SingleValue::getRandomValue();

        $this->assertSame(SingleValue::KEY, $value);
    }

    public function test_can_get_random_instance()
    {
        $instance = SingleValue::getRandomInstance();

        $this->assertTrue(
            $instance->is(SingleValue::KEY)
        );
    }
}
