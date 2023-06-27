<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Tests\Enums\UserType;
use Illuminate\Support\Facades\Validator;

final class EnumPipeValidationTest extends ApplicationTestCase
{
    public function testCanValidateValueUsingPipeValidation(): void
    {
        $validator = Validator::make(['type' => UserType::Administrator], [
            'type' => 'enum_value:' . UserType::class,
        ]);

        $this->assertTrue($validator->passes());

        $validator = Validator::make(['type' => 99], [
            'type' => 'enum_value:' . UserType::class,
        ]);

        $this->assertFalse($validator->passes());
    }

    public function testCanValidateValueUsingPipeValidationWithoutStrictTypeChecking(): void
    {
        $validator = Validator::make(['type' => (string) UserType::Administrator], [
            'type' => 'enum_value:' . UserType::class . ',false',
        ]);

        $this->assertTrue($validator->passes());
    }

    public function testCanValidateKeyUsingPipeValidation(): void
    {
        $validator = Validator::make(['type' => UserType::getKey(UserType::Administrator)], [
            'type' => 'enum_key:' . UserType::class,
        ]);

        $this->assertTrue($validator->passes());

        $validator = Validator::make(['type' => 'wrong'], [
            'type' => 'enum_key:' . UserType::class,
        ]);

        $this->assertFalse($validator->passes());
    }

    public function testCanValidateEnumUsingPipeValidation(): void
    {
        $validator = Validator::make(['type' => UserType::Administrator()], [
            'type' => 'enum:' . UserType::class,
        ]);

        $this->assertTrue($validator->passes());

        $validator = Validator::make(['type' => 'wrong'], [
            'type' => 'enum:' . UserType::class,
        ]);

        $this->assertFalse($validator->passes());
    }
}
