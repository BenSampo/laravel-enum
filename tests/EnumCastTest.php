<?php

namespace BenSampo\Enum\Tests;

use PHPUnit\Framework\TestCase;
use BenSampo\Enum\Tests\Enums\UserType;
use BenSampo\Enum\Tests\Models\Example;
use BenSampo\Enum\Tests\Models\ExampleArrayable;
use BenSampo\Enum\Exceptions\InvalidEnumMemberException;

class EnumCastTest extends TestCase
{
    public function test_model_can_detect_which_attributes_to_cast_to_an_enum()
    {
        $model = app(Example::class);

        $this->assertTrue($model->hasEnumCast('user_type'));
        $this->assertFalse($model->hasEnumCast('doesnt_exist'));
    }

    public function test_can_set_model_value_using_enum_instance()
    {
        $model = app(Example::class);
        $model->user_type = UserType::Moderator();

        $this->assertEquals(UserType::Moderator(), $model->user_type);
    }

    public function test_can_set_model_value_using_enum_instance_array()
    {
        $model = app(ExampleArrayable::class);
        $model->user_types = [UserType::Moderator(), UserType::Administrator()];

        $this->assertEquals([UserType::Moderator(), UserType::Administrator()], $model->user_types);
    }

    public function test_can_set_model_value_using_enum_value()
    {
        $model = app(Example::class);
        $model->user_type = UserType::Moderator;

        $this->assertEquals(UserType::Moderator(), $model->user_type);
    }

    public function test_can_set_model_value_using_enum_value_array()
    {
        $model = app(ExampleArrayable::class);
        $model->user_types = [UserType::Moderator, UserType::Administrator];

        $this->assertEquals([UserType::Moderator(), UserType::Administrator()], $model->user_types);
    }

    public function test_cannot_set_model_value_using_invalid_enum_value()
    {
        $this->expectException(InvalidEnumMemberException::class);

        $model = app(Example::class);
        $model->user_type = 5;

    }

    public function test_cannot_set_model_value_using_invalid_enum_value_array()
    {
        $this->expectException(InvalidEnumMemberException::class);

        $model = app(ExampleArrayable::class);
        $model->user_types = [1, 5];
    }

    public function test_getting_model_value_returns_enum_instance()
    {
        $model = app(Example::class);
        $model->user_type = UserType::Moderator;

        $this->assertInstanceOf(UserType::class, $model->user_type);
    }

    public function test_getting_model_value_returns_enum_instance_array()
    {
        $model = app(ExampleArrayable::class);
        $model->user_types = [UserType::Moderator, UserType::Administrator];

        $this->assertContainsOnlyInstancesOf(UserType::class, $model->user_types);
    }

    public function test_can_get_and_set_null_on_enum_castable()
    {
        $model = app(Example::class);
        $model->user_type = null;

        $this->assertNull($model->user_type);
    }

    public function test_can_get_and_set_null_on_enum_castable_array()
    {
        $model = app(ExampleArrayable::class);
        $model->user_types = null;

        $this->assertNull($model->user_types);
    }

    public function test_that_model_with_enum_can_be_cast_to_array()
    {
        $model = app(Example::class);
        $model->user_type = UserType::Moderator();

        $this->assertSame(['user_type' => 1], $model->toArray());
    }

    public function test_that_model_with_enum_array_can_be_cast_to_array()
    {
        $model = app(ExampleArrayable::class);
        $model->user_types = [UserType::Moderator(), UserType::Administrator()];

        $this->assertSame(['user_types' => [1, 0]], $model->toArray());
    }
}
