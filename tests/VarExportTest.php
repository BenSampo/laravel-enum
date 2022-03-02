<?php

namespace BenSampo\Enum\Tests;

use PHPUnit\Framework\TestCase;
use BenSampo\Enum\Tests\Enums\UserType;

class VarExportTest extends TestCase
{
    public function test_var_export()
    {
        $admin = UserType::Administrator();

        $exported = var_export($admin, true);
        $restored = eval("return {$exported};");

        $this->assertSame($admin->value, $restored->value);
    }
}
