<?php

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Rules\EnumKey;
use BenSampo\Enum\Tests\Enums\UserType;
use Illuminate\Support\Facades\Validator;

class EnumPipeValidationTest extends ApplicationTestCase
{
    public function test_can_validate_value_using_pipe_validation()
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

    public function test_can_validate_value_using_pipe_validation_without_strict_type_checking()
    {
        $validator = Validator::make(['type' => (string) UserType::Administrator], [
            'type' => 'enum_value:' . UserType::class . ',false',
        ]);

        $this->assertTrue($validator->passes());
    }

    public function test_can_validate_key_using_pipe_validation()
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

    public function test_can_validate_enum_using_pipe_validation()
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
