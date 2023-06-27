<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Tests\Enums\UserType;
use PHPUnit\Framework\TestCase;

final class EnumTypeHintTest extends TestCase
{
    public function testCanPassAnEnumInstanceToATypeHintedMethod(): void
    {
        $userType1 = UserType::fromValue(UserType::SuperAdministrator);
        $userType2 = UserType::fromValue(UserType::Moderator);

        $this->assertTrue($this->typeHintedMethod($userType1));
        $this->assertFalse($this->typeHintedMethod($userType2));
    }

    private function typeHintedMethod(UserType $userType): bool
    {
        if ($userType->is(UserType::SuperAdministrator)) {
            return true;
        }

        return false;
    }
}
