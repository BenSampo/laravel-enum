<?php

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Tests\Enums\UserTypeLocalized;
use PHPUnit\Framework\TestCase;

class EnumLocalizationUnitTest extends TestCase
{
    public function test_enum_get_description_with_localization()
    {
        $this->assertSame('Super administrator', UserTypeLocalized::getDescription(UserTypeLocalized::SuperAdministrator));
    }
}
