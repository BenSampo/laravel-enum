<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use PHPUnit\Framework\TestCase;
use BenSampo\Enum\Tests\Enums\UserType;

final class VarExportTest extends TestCase
{
    public function test_var_export(): void
    {
        $admin = UserType::Administrator();

        $exported = var_export($admin, true);
        $restored = eval("return {$exported};");

        $this->assertSame($admin->value, $restored->value);
    }
}
