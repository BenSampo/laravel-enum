<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Exceptions\InvalidEnumMemberException;
use BenSampo\Enum\Tests\Enums\UserType;
use BenSampo\Enum\Tests\Models\Example;

final class EnumCastTest extends ApplicationTestCase
{
    public function testCanSetModelValueUsingEnumInstance(): void
    {
        $model = new Example();
        $model->user_type = UserType::Moderator();

        $this->assertEquals(UserType::Moderator(), $model->user_type);
    }

    public function testCanSetModelValueUsingEnumValue(): void
    {
        $model = new Example();
        // @phpstan-ignore-next-line loose typing
        $model->user_type = UserType::Moderator;

        $this->assertEquals(UserType::Moderator(), $model->user_type);
    }

    public function testCannotSetModelValueUsingInvalidEnumValue(): void
    {
        $model = new Example();

        $this->expectException(InvalidEnumMemberException::class);
        // @phpstan-ignore-next-line intentionally wrong
        $model->user_type = 5;
    }

    public function testGettingModelValueReturnsEnumInstance(): void
    {
        $model = new Example();
        // @phpstan-ignore-next-line loose typing
        $model->user_type = UserType::Moderator;

        // @phpstan-ignore-next-line casts change the set property
        $this->assertInstanceOf(UserType::class, $model->user_type);
    }

    public function testCanGetAndSetNullOnEnumCastable(): void
    {
        $model = new Example();
        $model->user_type = null;

        $this->assertNull($model->user_type);
    }

    public function testThatModelWithEnumCanBeCastToArray(): void
    {
        $model = new Example();
        $model->user_type = UserType::Moderator();

        $this->assertSame(['user_type' => 1], $model->toArray());
    }

    public function testGetChangesWorksCorrectly(): void
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
