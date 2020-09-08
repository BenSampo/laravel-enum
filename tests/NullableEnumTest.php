<?php

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Exceptions\InvalidEnumMemberException;
use BenSampo\Enum\Tests\Enums\UserTypeNullable;
use BenSampo\Enum\Tests\Models\NativeCastModel;

class NullableEnumTest extends ApplicationTestCase
{
    public function test_can_get_and_set_null_on_enum_castable()
    {
        $model = app(NativeCastModel::class);
        $model->user_type_nullable = null;

        $this->assertInstanceOf(UserTypeNullable::class, $model->user_type_nullable);
    }

    public function test_can_handle_null_from_database()
    {
        /** @var NativeCastModel $model */
        $model = app(NativeCastModel::class);

        $reflection = new \ReflectionProperty(NativeCastModel::class, 'attributes');
        $reflection->setAccessible(true);
        $reflection->setValue($model, ['user_type_nullable' => null]);

        $this->assertInstanceOf(UserTypeNullable::class, $model->user_type_nullable);
    }

    public function test_cannot_set_model_value_using_invalid_enum_value()
    {
        $this->expectException(InvalidEnumMemberException::class);

        /** @var NativeCastModel $model */
        $model = app(NativeCastModel::class);

        $reflection = new \ReflectionProperty(NativeCastModel::class, 'attributes');
        $reflection->setAccessible(true);
        $reflection->setValue($model, ['user_type_nullable' => '61']);

        $this->assertInstanceOf(UserTypeNullable::class, $model->user_type_nullable);
    }

    public function test_null_value_enum_is_instance_of_enum_value()
    {
        $this->assertInstanceOf(UserTypeNullable::class, new UserTypeNullable(null));
    }
}
