<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Tests\Enums\UserType;
use BenSampo\Enum\Tests\Models\Example;
use BenSampo\Enum\Exceptions\InvalidEnumMemberException;

class EnumCastTest extends ApplicationTestCase
{
    public function test_can_set_model_value_using_enum_instance()
    {
        $model = new Example;
        $model->user_type = UserType::Moderator();

        $this->assertEquals(UserType::Moderator(), $model->user_type);
    }

    public function test_can_set_model_value_using_enum_value()
    {
        $model = new Example;
        $model->user_type = UserType::Moderator;

        $this->assertEquals(UserType::Moderator(), $model->user_type);
    }

    public function test_cannot_set_model_value_using_invalid_enum_value()
    {
        $this->expectException(InvalidEnumMemberException::class);

        $model = new Example;
        $model->user_type = 5;
    }

    public function test_getting_model_value_returns_enum_instance()
    {
        $model = new Example;
        $model->user_type = UserType::Moderator;

        $this->assertInstanceOf(UserType::class, $model->user_type);
    }

    public function test_can_get_and_set_null_on_enum_castable()
    {
        $model = new Example;
        $model->user_type = null;

        $this->assertNull($model->user_type);
    }

    public function test_that_model_with_enum_can_be_cast_to_array()
    {
        $model = new Example;
        $model->user_type = UserType::Moderator();

        $this->assertSame(['user_type' => 1], $model->toArray());
    }

    public function test_get_changes_works_correctly()
    {
        $id = Example::create(['user_type' => 1])->id;

        $model = Example::find($id);

        $this->assertEquals(UserType::Moderator(), $model->user_type);
        $this->assertEmpty($model->getChanges());

        $model->user_type = 1;
        $this->assertEmpty($model->getChanges());
        $model->save();

        $this->assertEquals(UserType::Moderator(), $model->user_type);
        $this->assertEmpty($model->getChanges());
    }
}
