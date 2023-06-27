<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Tests\Enums\UserType;
use PHPUnit\Framework\TestCase;

final class VarExportTest extends TestCase
{
    public function testVarExport(): void
    {
        $admin = UserType::Administrator();

        $exported = var_export($admin, true);
        $restored = eval("return {$exported};");

        $this->assertSame($admin->value, $restored->value);
    }
}
