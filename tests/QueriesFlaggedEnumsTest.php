<?php declare(strict_types=1);

namespace BenSampo\Enum\Tests;

use Orchestra\Testbench\TestCase;
use Illuminate\Foundation\Application;
use Illuminate\Database\Schema\Blueprint;
use BenSampo\Enum\Tests\Enums\SuperPowers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use BenSampo\Enum\Tests\Models\WithQueriesFlaggedEnums as TestModel;

final class QueriesFlaggedEnumsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);

        TestModel::create([
            'superpowers' =>  SuperPowers::flags([SuperPowers::Flight, SuperPowers::Immortality])
        ]);

        TestModel::create([
            'superpowers' =>  SuperPowers::flags([SuperPowers::Strength, SuperPowers::Immortality])
        ]);
    }

    protected function setUpDatabase(Application $app): void
    {
        $app['db']->connection()->getSchemaBuilder()->create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('superpowers');
            $table->timestamps();
        });
    }

    public function test_ensure_a_flag_is_present(): void
    {
        $this->assertSame(2, TestModel::query()->hasFlag('superpowers', SuperPowers::Immortality)->count());
        $this->assertSame(2, TestModel::query()->hasFlag('superpowers', SuperPowers::Immortality())->count());
        $this->assertSame(1, TestModel::query()->hasFlag('superpowers', SuperPowers::Flight)->count());
        $this->assertSame(1, TestModel::query()->hasFlag('superpowers', SuperPowers::Flight())->count());
        $this->assertSame(0, TestModel::query()->hasFlag('superpowers', SuperPowers::Invisibility)->count());
        $this->assertSame(0, TestModel::query()->hasFlag('superpowers', SuperPowers::Invisibility())->count());
    }

    public function test_ensure_a_flag_is_missing(): void
    {
        $this->assertSame(0, TestModel::query()->notHasFlag('superpowers', SuperPowers::Immortality)->count());
        $this->assertSame(0, TestModel::query()->notHasFlag('superpowers', SuperPowers::Immortality())->count());
        $this->assertSame(1, TestModel::query()->notHasFlag('superpowers', SuperPowers::Flight)->count());
        $this->assertSame(1, TestModel::query()->notHasFlag('superpowers', SuperPowers::Flight())->count());
        $this->assertSame(2, TestModel::query()->notHasFlag('superpowers', SuperPowers::Invisibility)->count());
        $this->assertSame(2, TestModel::query()->notHasFlag('superpowers', SuperPowers::Invisibility())->count());
    }

    public function test_ensure_all_flags_are_present(): void
    {
        $this->assertSame(0, TestModel::query()->hasAllFlags('superpowers', [SuperPowers::Strength, SuperPowers::Flight])->count());
        $this->assertSame(0, TestModel::query()->hasAllFlags('superpowers', [SuperPowers::Strength(), SuperPowers::Flight()])->count());
        $this->assertSame(1, TestModel::query()->hasAllFlags('superpowers', [SuperPowers::Immortality, SuperPowers::Flight])->count());
        $this->assertSame(1, TestModel::query()->hasAllFlags('superpowers', [SuperPowers::Immortality(), SuperPowers::Flight()])->count());
        $this->assertSame(2, TestModel::query()->hasAllFlags('superpowers', [SuperPowers::Immortality])->count());
        $this->assertSame(2, TestModel::query()->hasAllFlags('superpowers', [SuperPowers::Immortality()])->count());
    }

    public function test_ensure_any_flag_is_present(): void
    {
        $this->assertSame(2, TestModel::query()->hasAnyFlags('superpowers', [SuperPowers::Strength, SuperPowers::Flight])->count());
        $this->assertSame(2, TestModel::query()->hasAnyFlags('superpowers', [SuperPowers::Strength(), SuperPowers::Flight()])->count());
        $this->assertSame(1, TestModel::query()->hasAnyFlags('superpowers', [SuperPowers::Invisibility, SuperPowers::Flight])->count());
        $this->assertSame(1, TestModel::query()->hasAnyFlags('superpowers', [SuperPowers::Invisibility(), SuperPowers::Flight()])->count());
        $this->assertSame(2, TestModel::query()->hasAnyFlags('superpowers', [SuperPowers::Immortality])->count());
        $this->assertSame(2, TestModel::query()->hasAnyFlags('superpowers', [SuperPowers::Immortality()])->count());
    }
}
