<?php

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Exceptions\InvalidEnumMemberException;
use BenSampo\Enum\Tests\Enums\UserType;
use BenSampo\Enum\Tests\Enums\UserTypeCustomCast;
use BenSampo\Enum\Tests\Models\NativeCastModel;
use PHPUnit\Framework\TestCase;

class NativeEnumCastTest extends TestCase
{
    public function test_can_set_model_value_using_enum_instance()
    {
        $model = app(NativeCastModel::class);
        $model->user_type = UserType::Moderator();

        $this->assertEquals(UserType::Moderator(), $model->user_type);
    }

    public function test_can_set_model_value_using_enum_value()
    {
        $model = app(NativeCastModel::class);
        $model->user_type = UserType::Moderator;

        $this->assertEquals(UserType::Moderator(), $model->user_type);
    }

    public function test_cannot_set_model_value_using_invalid_enum_value()
    {
        $this->expectException(InvalidEnumMemberException::class);

        $model = app(NativeCastModel::class);
        $model->user_type = 5;
    }

    public function test_getting_model_value_returns_enum_instance()
    {
        $model = app(NativeCastModel::class);
        $model->user_type = UserType::Moderator;

        $this->assertInstanceOf(UserType::class, $model->user_type);
    }

    public function test_can_get_and_set_null_on_enum_castable()
    {
        $model = app(NativeCastModel::class);
        $model->user_type = null;

        $this->assertNull($model->user_type);
    }

    public function test_can_handle_string_int_from_database()
    {
        /** @var NativeCastModel $model */
        $model = app(NativeCastModel::class);

        $reflection = new \ReflectionProperty(NativeCastModel::class, 'attributes');
        $reflection->setAccessible(true);
        $reflection->setValue($model, ['user_type' => '1']);

        $this->assertInstanceOf(UserType::class, $model->user_type);
    }

    public function test_that_model_with_enum_can_be_cast_to_array()
    {
        $model = app(NativeCastModel::class);
        $model->user_type = UserType::Moderator();

        $this->assertSame(['user_type' => 1], $model->toArray());
    }

    public function test_can_use_custom_casting()
    {
        /** @var NativeCastModel $model */
        $model = app(NativeCastModel::class);

        $reflection = new \ReflectionProperty(NativeCastModel::class, 'attributes');
        $reflection->setAccessible(true);
        $reflection->setValue($model, ['user_type_custom' => 'type-3']);

        $this->assertInstanceOf(UserTypeCustomCast::class, $model->user_type_custom);
        $this->assertEquals(UserTypeCustomCast::SuperAdministrator(), $model->user_type_custom);

        $model->user_type_custom = UserTypeCustomCast::Administrator();

        $this->assertSame('type-0', $reflection->getValue($model)['user_type_custom']);
    }

    public function test_can_bail_custom_casting()
    {
        /** @var NativeCastModel $model */
        $model = app(NativeCastModel::class);

        $reflection = new \ReflectionProperty(NativeCastModel::class, 'attributes');
        $reflection->setAccessible(true);
        $reflection->setValue($model, ['user_type_custom' => '']);

        $this->assertNull($model->user_type_custom);
    }
}
