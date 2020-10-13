<?php

namespace BenSampo\Enum\Tests;

use BenSampo\Enum\Tests\Enums\SuperPowers;
use BenSampo\Enum\Tests\Models\WithQueriesFlaggedEnums as TestModel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase;

class QueriesFlaggedEnumsTest extends TestCase
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

    protected function setUpDatabase(Application $app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('superpowers');
            $table->timestamps();
        });
    }
    
    /** @test */
    public function it_can_ensure_a_flag_is_present()
    {
        $this->assertEquals(2, TestModel::query()->hasFlag('superpowers', SuperPowers::Immortality)->count());
        $this->assertEquals(1, TestModel::query()->hasFlag('superpowers', SuperPowers::Flight)->count());
        $this->assertEquals(0, TestModel::query()->hasFlag('superpowers', SuperPowers::Invisibility)->count());
    }

    /** @test */
    public function it_can_ensure_a_flag_is_missing()
    {
        $this->assertEquals(0, TestModel::query()->notHasFlag('superpowers', SuperPowers::Immortality)->count());
        $this->assertEquals(1, TestModel::query()->notHasFlag('superpowers', SuperPowers::Flight)->count());
        $this->assertEquals(2, TestModel::query()->notHasFlag('superpowers', SuperPowers::Invisibility)->count());
    }

    /** @test */
    public function it_can_ensure_all_flags_are_present()
    {
        $this->assertEquals(0, TestModel::query()->hasAllFlags('superpowers', [SuperPowers::Strength, SuperPowers::Flight])->count());
        $this->assertEquals(1, TestModel::query()->hasAllFlags('superpowers', [SuperPowers::Immortality, SuperPowers::Flight])->count());
        $this->assertEquals(2, TestModel::query()->hasAllFlags('superpowers', [SuperPowers::Immortality])->count());
    }

    /** @test */
    public function it_can_ensure_any_flag_is_present()
    {       
        $this->assertEquals(2, TestModel::query()->hasAnyFlags('superpowers', [SuperPowers::Strength, SuperPowers::Flight])->count());
        $this->assertEquals(1, TestModel::query()->hasAnyFlags('superpowers', [SuperPowers::Invisibility, SuperPowers::Flight])->count());
        $this->assertEquals(2, TestModel::query()->hasAnyFlags('superpowers', [SuperPowers::Immortality])->count());
    }
}
